<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use InvalidArgumentException;

final class PracticeAreaDetailRepository
{
    public function __construct(private readonly Database $database)
    {
    }

    public function adminArea(int $id): ?array
    {
        $row = $this->database->statement(
            'SELECT * FROM practice_areas WHERE id = :id LIMIT 1',
            ['id' => $id]
        )->fetch();

        return $row ?: null;
    }

    public function editorData(int $areaId): array
    {
        return [
            'contacts' => $this->database->statement(
                'SELECT advocate_id FROM practice_area_contacts WHERE practice_area_id = :id ORDER BY sort_order ASC, id ASC',
                ['id' => $areaId]
            )->fetchAll(\PDO::FETCH_COLUMN),
            'experience' => $this->database->statement(
                'SELECT content FROM practice_area_experience WHERE practice_area_id = :id ORDER BY sort_order ASC, id ASC',
                ['id' => $areaId]
            )->fetchAll(\PDO::FETCH_COLUMN),
            'insights' => $this->database->statement(
                'SELECT article_id FROM practice_area_insights WHERE practice_area_id = :id ORDER BY sort_order ASC, id ASC',
                ['id' => $areaId]
            )->fetchAll(\PDO::FETCH_COLUMN),
            'related' => $this->database->statement(
                'SELECT related_practice_area_id FROM practice_area_related WHERE practice_area_id = :id ORDER BY sort_order ASC, id ASC',
                ['id' => $areaId]
            )->fetchAll(\PDO::FETCH_COLUMN),
        ];
    }

    public function advocates(): array
    {
        return $this->database->statement(
            'SELECT id, first_name, last_name, title, email FROM advocates ORDER BY first_name ASC, last_name ASC'
        )->fetchAll();
    }

    public function articles(): array
    {
        return $this->database->statement(
            'SELECT id, title, status, published_at FROM articles ORDER BY published_at DESC, id DESC'
        )->fetchAll();
    }

    public function areasExcept(int $areaId): array
    {
        return $this->database->statement(
            'SELECT id, name FROM practice_areas WHERE id != :id ORDER BY name ASC',
            ['id' => $areaId]
        )->fetchAll();
    }

    public function save(int $areaId, array $data): void
    {
        $contacts = $this->uniqueIds($data['contacts'] ?? []);
        $insights = $this->uniqueIds($data['insights'] ?? []);
        $related = array_values(array_filter(
            $this->uniqueIds($data['related'] ?? []),
            static fn (int $id): bool => $id !== $areaId
        ));
        $experience = $this->experience($data['experience'] ?? []);

        $pdo = $this->database->pdo();
        $pdo->beginTransaction();

        try {
            foreach (['practice_area_contacts', 'practice_area_experience', 'practice_area_insights', 'practice_area_related'] as $table) {
                $this->database->statement(
                    'DELETE FROM ' . $table . ' WHERE practice_area_id = :id',
                    ['id' => $areaId]
                );
            }

            foreach ($contacts as $order => $advocateId) {
                $this->database->statement(
                    'INSERT INTO practice_area_contacts (practice_area_id, advocate_id, sort_order) VALUES (:area, :advocate, :sort_order)',
                    ['area' => $areaId, 'advocate' => $advocateId, 'sort_order' => $order]
                );
            }

            foreach ($experience as $order => $content) {
                $this->database->statement(
                    'INSERT INTO practice_area_experience (practice_area_id, content, sort_order) VALUES (:area, :content, :sort_order)',
                    ['area' => $areaId, 'content' => $content, 'sort_order' => $order]
                );
            }

            foreach ($insights as $order => $articleId) {
                $this->database->statement(
                    'INSERT INTO practice_area_insights (practice_area_id, article_id, sort_order) VALUES (:area, :article, :sort_order)',
                    ['area' => $areaId, 'article' => $articleId, 'sort_order' => $order]
                );
            }

            foreach ($related as $order => $relatedId) {
                $this->database->statement(
                    'INSERT INTO practice_area_related (practice_area_id, related_practice_area_id, sort_order) VALUES (:area, :related, :sort_order)',
                    ['area' => $areaId, 'related' => $relatedId, 'sort_order' => $order]
                );
            }

            $pdo->commit();
        } catch (\Throwable $exception) {
            $pdo->rollBack();
            throw $exception;
        }
    }

    public function publicDetails(int $areaId): array
    {
        return [
            'contacts' => $this->database->statement(
                'SELECT a.* FROM practice_area_contacts pac
                 INNER JOIN advocates a ON a.id = pac.advocate_id
                 WHERE pac.practice_area_id = :id AND a.is_enabled = 1
                 ORDER BY pac.sort_order ASC, pac.id ASC',
                ['id' => $areaId]
            )->fetchAll(),
            'experience' => $this->database->statement(
                'SELECT content FROM practice_area_experience WHERE practice_area_id = :id ORDER BY sort_order ASC, id ASC',
                ['id' => $areaId]
            )->fetchAll(),
            'insights' => $this->database->statement(
                'SELECT a.* FROM practice_area_insights pai
                 INNER JOIN articles a ON a.id = pai.article_id
                 WHERE pai.practice_area_id = :id AND a.is_enabled = 1
                   AND a.status = "published" AND a.published_at IS NOT NULL AND a.published_at <= NOW()
                 ORDER BY pai.sort_order ASC, pai.id ASC',
                ['id' => $areaId]
            )->fetchAll(),
            'related' => $this->database->statement(
                'SELECT pa.* FROM practice_area_related par
                 INNER JOIN practice_areas pa ON pa.id = par.related_practice_area_id
                 WHERE par.practice_area_id = :id AND pa.is_enabled = 1
                 ORDER BY par.sort_order ASC, par.id ASC',
                ['id' => $areaId]
            )->fetchAll(),
        ];
    }

    private function uniqueIds(mixed $values): array
    {
        $values = is_array($values) ? $values : [];
        $ids = [];

        foreach ($values as $value) {
            $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

            if ($id !== false) {
                $ids[] = (int) $id;
            }
        }

        return array_values(array_unique($ids));
    }

    private function experience(mixed $values): array
    {
        $values = is_array($values) ? $values : [];
        $items = [];

        foreach ($values as $value) {
            $value = trim((string) $value);

            if ($value !== '') {
                $items[] = $value;
            }
        }

        return $items;
    }
}
