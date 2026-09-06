<?php
declare(strict_types=1);

return static function (\PDO $pdo): void {
    $root=dirname(__DIR__,2);
    $rows=$pdo->query('SELECT id, path FROM media WHERE checksum IS NULL OR checksum = ""')->fetchAll(\PDO::FETCH_ASSOC);
    $updateChecksum=$pdo->prepare('UPDATE media SET checksum = :checksum WHERE id = :id');

    foreach($rows as $row){
        $file=$root.'/public_html'.(string)$row['path'];
        if(!is_file($file))continue;
        $checksum=hash_file('sha256',$file);
        if($checksum!==false)$updateChecksum->execute(['checksum'=>$checksum,'id'=>(int)$row['id']]);
    }

    $duplicates=$pdo->query('SELECT checksum FROM media WHERE checksum IS NOT NULL AND checksum != "" GROUP BY checksum HAVING COUNT(*) > 1')->fetchAll(\PDO::FETCH_COLUMN);
    $references=$pdo->query('SELECT TABLE_NAME, COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND REFERENCED_TABLE_NAME = "media" AND REFERENCED_COLUMN_NAME = "id"')->fetchAll(\PDO::FETCH_ASSOC);
    $filesToDelete=[];
    $pdo->beginTransaction();

    try {
        foreach($duplicates as $checksum){
            $media=$pdo->prepare('SELECT id, path FROM media WHERE checksum = :checksum ORDER BY id ASC');
            $media->execute(['checksum'=>$checksum]);
            $items=$media->fetchAll(\PDO::FETCH_ASSOC);
            if(count($items)<2)continue;
            $canonicalId=(int)$items[0]['id'];

            foreach(array_slice($items,1) as $duplicate){
                $duplicateId=(int)$duplicate['id'];

                foreach($references as $reference){
                    $table=(string)$reference['TABLE_NAME'];
                    $column=(string)$reference['COLUMN_NAME'];
                    if(preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/',$table)!==1||preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/',$column)!==1){
                        throw new \RuntimeException('Invalid media reference discovered during deduplication.');
                    }

                    $statement=$pdo->prepare('UPDATE '.$table.' SET '.$column.' = :canonical WHERE '.$column.' = :duplicate');
                    $statement->execute(['canonical'=>$canonicalId,'duplicate'=>$duplicateId]);
                }

                $delete=$pdo->prepare('DELETE FROM media WHERE id = :id');
                $delete->execute(['id'=>$duplicateId]);
                $filesToDelete[]=$root.'/public_html'.(string)$duplicate['path'];
            }
        }

        $checksumIndexes=$pdo->query('SELECT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "media" AND COLUMN_NAME = "checksum" GROUP BY INDEX_NAME')->fetchAll(\PDO::FETCH_COLUMN);
        if(in_array('media_checksum_index',$checksumIndexes,true))$pdo->exec('ALTER TABLE media DROP INDEX media_checksum_index');

        $uniqueExists=$pdo->query('SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "media" AND INDEX_NAME = "media_checksum_unique" LIMIT 1')->fetchColumn();
        if(!$uniqueExists)$pdo->exec('ALTER TABLE media ADD UNIQUE KEY media_checksum_unique (checksum)');

        $pdo->commit();
    } catch(\Throwable $exception) {
        if($pdo->inTransaction())$pdo->rollBack();
        throw $exception;
    }

    foreach($filesToDelete as $file){if(is_file($file))@unlink($file);}
};
