# Q400 System Study Database Seeds

## Overview

This directory contains structured JSON data extracted from Q400 aircraft system PDFs. All data is ready for import into a PHP study application.

## Files

### Content Files (22 total)
- `content_*.json` - Individual system data files
  - One file per Q400 system
  - Named using sanitized system names (lowercase, underscores)

### Index File
- `systems_index.json` - Master index with metadata for all systems

## Data Structure

### Content File Format
```json
{
  "system_name": "Electrical Power",
  "ata_code": "ATA24",
  "pdf_file": "Q400-Electrical.pdf",
  "full_text": "... extracted text ...",
  "full_text_truncated": false,
  "word_count": 11453,
  "sections": [
    {
      "title": "Introduction",
      "content": "..."
    }
  ],
  "components": ["Generator", "Battery", "TRU", ...],
  "key_facts": ["28VDC distribution", "NiCad batteries", ...]
}
```

### Index File Format
```json
{
  "systems": [
    {
      "name": "Electrical Power",
      "ata_code": "ATA24",
      "pdf_file": "Q400-Electrical.pdf",
      "content_file": "content_electrical_power.json",
      "word_count": 11453,
      "sections_count": 67,
      "components_count": 50,
      "facts_count": 30
    }
  ]
}
```

## Statistics

- **Total Systems**: 22
- **Total Words**: 325,657
- **Total Sections**: 1,449
- **Total Components**: 951
- **Total Facts**: 402

## Systems Included

| Code | System | File |
|------|--------|------|
| ATA21 | Aeroplane General | content_aeroplane_general.json |
| ATA21 | Air Conditioning & Pressurization | content_air_conditioning_and_pressurization.json |
| ATA22 | Autoflight | content_autoflight.json |
| ATA22B | FMS | content_fms.json |
| ATA23 | Communications | content_communications.json |
| ATA24 | Electrical Power | content_electrical_power.json |
| ATA26 | Fire Protection | content_fire_protection.json |
| ATA27 | Flight Controls | content_flight_controls.json |
| ATA28 | Fuel | content_fuel.json |
| ATA29 | Hydraulic Power | content_hydraulic_power.json |
| ATA30 | Ice & Rain Protection | content_ice_and_rain_protection.json |
| ATA31 | Indicating & Recording | content_indicating_and_recording.json |
| ATA32 | Landing Gear | content_landing_gear.json |
| ATA33 | Lighting | content_lighting.json |
| ATA34 | Navigation | content_navigation.json |
| ATA35 | Oxygen | content_oxygen.json |
| ATA36 | Pneumatics | content_pneumatics.json |
| ATA61 | Propeller | content_propeller.json |
| ATA71 | Powerplant | content_powerplant.json |
| CW | Caution & Warning Messages | content_caution_and_warning_messages.json |
| DU | DU Messages | content_du_messages.json |
| QRH | Quick Reference Handbook | content_quick_reference_handbook.json |

## PHP Integration Example

### Loading the Index
```php
<?php
$index_json = file_get_contents('seeds/systems_index.json');
$systems = json_decode($index_json, true)['systems'];

foreach ($systems as $system) {
    echo $system['name'] . " (" . $system['ata_code'] . ")";
}
?>
```

### Loading System Content
```php
<?php
$content_json = file_get_contents('seeds/content_electrical_power.json');
$system = json_decode($content_json, true);

echo $system['system_name'];
echo "Word Count: " . $system['word_count'];

foreach ($system['sections'] as $section) {
    echo $section['title'];
    echo $section['content'];
}

foreach ($system['components'] as $component) {
    // Store component names
}

foreach ($system['key_facts'] as $fact) {
    // Store study facts
}
?>
```

## Notes

- Large PDFs are truncated to ~150KB of text in JSON (full_text_truncated flag indicates this)
- All text extraction respects PDF metadata extraction restrictions (warnings are suppressed)
- Component extraction identifies technical terms appearing 2+ times
- Fact extraction finds sentences containing operational keywords
- All sections parsed from numbered headings and ALL CAPS headers

## Quality Assurance

All 22 JSON files have been verified for:
- Valid JSON structure
- Required fields present
- File completeness
- Index accuracy

Extraction completed: April 11, 2026
