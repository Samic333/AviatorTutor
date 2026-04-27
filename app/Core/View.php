<?php
/**
 * Template View Renderer
 *
 * Renders PHP templates with data extraction
 */

namespace App\Core;

class View
{
    /**
     * Base views directory
     */
    private string $viewPath;

    /**
     * Layout directory
     */
    private string $layoutPath;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->viewPath = BASE_PATH . '/views';
        $this->layoutPath = BASE_PATH . '/views/layouts';
    }

    /**
     * Render a template without layout
     *
     * @param string $template Template name (e.g., 'dashboard/index')
     * @param array $data Data to pass to template
     * @return string Rendered HTML
     * @throws Exception if template not found
     */
    public function render(string $template, array $data = []): string
    {
        $path = $this->viewPath . '/' . $template . '.php';

        if (!file_exists($path)) {
            throw new \Exception("Template not found: {$path}");
        }

        // Start output buffering
        ob_start();

        // Extract data into variables
        extract($data, EXTR_SKIP);

        // Include template
        require $path;

        // Get and clear buffer
        return ob_get_clean();
    }

    /**
     * Render with layout wrapper
     *
     * @param string $template Template name
     * @param string $layout Layout name (default: 'app')
     * @param array $data Data to pass to template
     * @return string Rendered HTML with layout
     */
    public function renderWithLayout(string $template, string $layout = 'app', array $data = []): string
    {
        // Render the main template
        $content = $this->render($template, $data);

        // Render the layout with content
        $layoutPath = $this->layoutPath . '/' . $layout . '.php';

        if (!file_exists($layoutPath)) {
            throw new \Exception("Layout not found: {$layoutPath}");
        }

        ob_start();

        // Extract data and content for layout
        extract($data, EXTR_SKIP);
        $content_for_layout = $content;

        require $layoutPath;

        return ob_get_clean();
    }

    /**
     * Include a partial template
     *
     * Use within templates to include partials
     * Example: <?php $this->partial('components/card', ['title' => 'Test']) ?>
     *
     * @param string $partial Partial name
     * @param array $data Data for partial
     * @return void
     */
    public function partial(string $partial, array $data = []): void
    {
        $path = $this->viewPath . '/' . $partial . '.php';

        if (!file_exists($path)) {
            throw new \Exception("Partial not found: {$path}");
        }

        extract($data, EXTR_SKIP);
        require $path;
    }

    /**
     * Escape HTML output
     *
     * Use in templates: <?php echo $this->escape($variable) ?>
     *
     * @param string $text Text to escape
     * @return string
     */
    public function escape(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generate a route URL
     *
     * @param string $path Route path
     * @param array $params URL parameters
     * @return string
     */
    public function route(string $path, array $params = []): string
    {
        $config = require BASE_PATH . '/config/app.php';
        $url = $config['base_url'] . $path;

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }

    /**
     * Generate a URL
     *
     * @param string $path Path
     * @return string
     */
    public function url(string $path): string
    {
        $config = require BASE_PATH . '/config/app.php';
        return $config['base_url'] . $path;
    }

    /**
     * Check if condition is true
     *
     * @param bool $condition
     * @return string 'checked' or empty
     */
    public function checked(bool $condition): string
    {
        return $condition ? 'checked' : '';
    }

    /**
     * Check if condition is true for selected
     *
     * @param bool $condition
     * @return string 'selected' or empty
     */
    public function selected(bool $condition): string
    {
        return $condition ? 'selected' : '';
    }

    /**
     * Check if condition is true for disabled
     *
     * @param bool $condition
     * @return string 'disabled' or empty
     */
    public function disabled(bool $condition): string
    {
        return $condition ? 'disabled' : '';
    }

    /**
     * Check if condition is true for active
     *
     * @param bool $condition
     * @return string 'active' or empty
     */
    public function active(bool $condition): string
    {
        return $condition ? 'active' : '';
    }

    /**
     * Get CSRF token field
     *
     * @return string
     */
    public function csrfField(): string
    {
        return CSRF::field();
    }

    /**
     * Truncate text
     *
     * @param string $text Text to truncate
     * @param int $length Maximum length
     * @param string $suffix Suffix for truncated text
     * @return string
     */
    public function truncate(string $text, int $length = 50, string $suffix = '...'): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length) . $suffix;
    }

    /**
     * Format date
     *
     * @param string|int $date Date string or timestamp
     * @param string $format Date format
     * @return string
     */
    public function date($date, string $format = 'Y-m-d H:i:s'): string
    {
        if (is_string($date)) {
            $date = strtotime($date);
        }

        return date($format, $date);
    }

    /**
     * Get view path
     *
     * @return string
     */
    public function getViewPath(): string
    {
        return $this->viewPath;
    }

    /**
     * Get layout path
     *
     * @return string
     */
    public function getLayoutPath(): string
    {
        return $this->layoutPath;
    }
}
