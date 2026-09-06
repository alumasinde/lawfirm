<?php
declare(strict_types=1);
namespace App\Support;

final class ImageOptimizer
{
    private const CONVERTIBLE_MIMES=['image/jpeg','image/png','image/webp'];

    public static function optimizeToWebp(string $source,string $mime,string $destination,int $maxDimension=2200,int $quality=82): ?array
    {
        if(!in_array($mime,self::CONVERTIBLE_MIMES,true)||!self::available()||!self::decoderAvailable($mime))return null;

        $image=match($mime){
            'image/jpeg'=>@imagecreatefromjpeg($source),
            'image/png'=>@imagecreatefrompng($source),
            'image/webp'=>@imagecreatefromwebp($source),
            default=>null,
        };

        if($image===false||$image===null)return null;

        try{
            $width=imagesx($image);$height=imagesy($image);
            if($width<1||$height<1)return null;

            $largest=max($width,$height);
            $scale=$largest>$maxDimension?$maxDimension/$largest:1.0;
            $targetWidth=max(1,(int)round($width*$scale));
            $targetHeight=max(1,(int)round($height*$scale));
            $target=imagecreatetruecolor($targetWidth,$targetHeight);
            if($target===false)return null;

            try{
                imagealphablending($target,false);
                imagesavealpha($target,true);
                $transparent=imagecolorallocatealpha($target,0,0,0,127);
                imagefill($target,0,0,$transparent);
                if(!imagecopyresampled($target,$image,0,0,0,0,$targetWidth,$targetHeight,$width,$height))return null;
                if(!@imagewebp($target,$destination,max(50,min(95,$quality))))return null;
            } finally {imagedestroy($target);}

            $dimensions=@getimagesize($destination);
            if($dimensions===false){@unlink($destination);return null;}

            return ['mime_type'=>'image/webp','width'=>(int)$dimensions[0],'height'=>(int)$dimensions[1],'size_bytes'=>(int)(filesize($destination)?:0)];
        } finally {imagedestroy($image);}
    }

    public static function available(): bool
    {
        return function_exists('imagecreatetruecolor')&&function_exists('imagecopyresampled')&&function_exists('imagewebp');
    }

    private static function decoderAvailable(string $mime): bool
    {
        return match($mime){
            'image/jpeg'=>function_exists('imagecreatefromjpeg'),
            'image/png'=>function_exists('imagecreatefrompng'),
            'image/webp'=>function_exists('imagecreatefromwebp'),
            default=>false,
        };
    }
}
