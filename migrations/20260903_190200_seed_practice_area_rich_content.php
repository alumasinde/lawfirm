<?php

declare(strict_types=1);

return static function (PDO $pdo): void {
    $pdo->exec(
        'UPDATE practice_areas
         SET overview_heading = COALESCE(NULLIF(overview_heading, ""), "Strategic legal support for your business"),
             approach_heading = COALESCE(NULLIF(approach_heading, ""), "Practical, commercially minded counsel"),
             cta_heading = COALESCE(NULLIF(cta_heading, ""), "Let us help you navigate the next step")'
    );
};
