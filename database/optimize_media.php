<?php
declare(strict_types=1);

use App\Support\ImageOptimizer;

if(PHP_SAPI!=='cli'){http_response_code(403);exit("This script can only be run from the command line.\n");}

$app=require dirname(__DIR__).'/app/bootstrap.php';
$pdo=$app->database()->pdo();

if(!ImageOptimizer::available()){
    fwrite(STDERR,"GD WebP support is required. Enable the PHP GD extension with WebP support and run again.\n");
    exit(1);
}

$rows=$pdo->query('SELECT id, path, mime_type FROM media ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
$updated=0;$skipped=0;
$update=$pdo->prepare('UPDATE media SET path = :path, mime_type = :mime_type, size_bytes = :size_bytes, width = :width, height = :height WHERE id = :id');

foreach($rows as $row){
    $mime=(string)$row['mime_type'];
    if(!in_array($mime,['image/jpeg','image/png','image/webp'],true)){$skipped++;continue;}

    $source=BASE_PATH.'/public_html'.(string)$row['path'];
    if(!is_file($source)){$skipped++;continue;}

    $directory=dirname($source);
    $basename=pathinfo($source,PATHINFO_FILENAME);
    $destination=$directory.'/'.$basename.'.optimized.webp';
    $optimized=ImageOptimizer::optimizeToWebp($source,$mime,$destination);

    if($optimized===null){$skipped++;continue;}

    $newPath=str_replace(BASE_PATH.'/public_html','',$destination);

    try {
        $update->execute([
            'path'=>$newPath,
            'mime_type'=>$optimized['mime_type'],
            'size_bytes'=>$optimized['size_bytes'],
            'width'=>$optimized['width'],
            'height'=>$optimized['height'],
            'id'=>(int)$row['id'],
        ]);
        @unlink($source);
        $updated++;
    } catch(Throwable $exception) {
        @unlink($destination);
        throw $exception;
    }
}

echo "Media optimization complete. Updated: {$updated}. Skipped: {$skipped}.\n";
