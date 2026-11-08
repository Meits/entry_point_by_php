<?php

declare(strict_types=1);

namespace App\System\View;

final class View implements IView
{
    public function __construct(
        private readonly string $basePath,
        private readonly string $templatePath,
    ) {
    }

    public function render(string $path, array $param = []): string
    {
        $file = sprintf('%s/%s/%s.php', $this->basePath, $this->templatePath, $path);

        if (!is_file($file)) {
            throw new \RuntimeException(sprintf('View template "%s" not found.', $file));
        }

        extract($param);
        ob_start();
        require $file;

        return ob_get_clean();
    }
}
