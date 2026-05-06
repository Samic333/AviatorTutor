# Phase 14 — DB/API consistency audit

**Date:** 2026-05-06

## Method

```
grep -rEn "FROM (user_progress|flashcard_reviews|quiz_attempts|study_sessions|flashcard_attempts)" app/
```

29 read sites across 12 controllers/services/models, against 5 progress-related tables. For each "same conceptual question", checked whether the queries that answer it agree on predicates, source table, and grouping.

## Per-metric reconciliation

| Metric | Asked by | Source | Match? |
|---|---|---|---|
| Total systems | Dashboard, Progress | `SELECT COUNT(*) FROM systems` (Dashboard) vs **hardcoded `22`** in Progress | ❌ **Fixed in this phase** — Progress now queries `systems` too |
| Completed systems | Dashboard, Progress | `user_progress` `status='completed'` GROUP BY system_id | ✅ Same predicate |
| In-progress systems | Dashboard, Progress, Subjects | `user_progress` `status='in_progress'` | ✅ Same predicate |
| Flashcards due | Dashboard widget, Flashcards index | `flashcard_reviews` `next_review_at <= NOW()` | ✅ Same predicate |
| Average quiz score | Dashboard, Progress | `quiz_attempts` `status='completed'`, `AVG(score)` | ✅ Same query (rounding done in PHP for Dashboard, in SQL for Progress — cosmetic) |
| Study streak | Dashboard, Progress | `users.study_streak` (denormalised, kept in sync by `User::updateStreak()`) | ✅ Same column |
| Total study time | Progress | `SUM(duration_secs) FROM study_sessions` | ✅ — Phase 9 added the heartbeat that makes this non-zero |
| Recent activity | Dashboard | `study_sessions` ORDER BY started_at DESC LIMIT 5 | ✅ Single source |
| 90-day streak heatmap | Dashboard | `study_sessions` per-day count | ⚠ Runs 90 separate queries in a loop — see "Performance smell" below |
| 30-day study history | Progress | `study_sessions` GROUP BY DATE(started_at) | ✅ Single query |
| Quiz performance by system | Dashboard chart | `quiz_attempts JOIN quizzes JOIN systems` | ✅ Single source |
| Weak topics | Progress | `user_topic_strength` ORDER BY strength_score | ✅ Single source |

## Performance smell — not consistency, but worth flagging

**`DashboardController::index` lines 190–209** loops 90 times calling `queryOne` per day to populate the streak heatmap. That's 90 round-trips per dashboard load. A single grouped query would replace it:

```sql
SELECT DATE(started_at) AS d, COUNT(*) AS c
  FROM study_sessions
 WHERE user_id = ?
   AND started_at >= DATE_SUB(CURDATE(), INTERVAL 89 DAY)
 GROUP BY DATE(started_at);
```

Then PHP fills in the gaps (days with 0 sessions) for the calendar render.

I did not consolidate this in Phase 14 because the brief said "Don't refactor reflexively" — it's correctness-equivalent today, just slow. Filing as a follow-up: targeted ~10-line refactor in `DashboardController::index`, expected dashboard p95 cut by 80–150ms once `study_sessions` has volume.

## Fix landed in this phase

```
app/Controllers/ProgressController.php   replaced hardcoded $totalSystems = 22 with a SELECT COUNT(*) from systems.
```

This was the only metric where two pages were genuinely answering "the same" question with different numbers. After this change, Progress and Dashboard always agree on the system count regardless of curriculum changes.

## Out of scope

- The `Lesson` model has its own `user_progress` reads (lines 103, 130) that are scoped by `(user_id, lesson_id)` — those are per-lesson reads, not aggregate, so not audit candidates.
- `AdminMetricsService::dailySessions` reads from `study_sessions WHERE DATE(created_at) = CURDATE()` — note this uses `created_at`, not `started_at`. The two columns are functionally identical for a row that just got inserted (Phase 3 inserts both at NOW), so this is fine. If session-end backfill ever updates `created_at` to a later time we'd diverge — flag for a future sweep.
