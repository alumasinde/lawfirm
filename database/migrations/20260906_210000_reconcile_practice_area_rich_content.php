<?php
declare(strict_types=1);

return static function (\PDO $pdo): void {
    $columns=$pdo->query('SHOW COLUMNS FROM practice_areas')->fetchAll(\PDO::FETCH_COLUMN);
    $definitions=[
        'overview_intro'=>'TEXT NULL AFTER excerpt',
        'overview_heading'=>'VARCHAR(255) NULL AFTER overview_intro',
        'approach_heading'=>'VARCHAR(255) NULL AFTER overview_heading',
        'approach_body'=>'TEXT NULL AFTER approach_heading',
        'cta_heading'=>'VARCHAR(255) NULL AFTER approach_body',
        'cta_body'=>'TEXT NULL AFTER cta_heading',
    ];

    foreach($definitions as $column=>$definition){
        if(!in_array($column,$columns,true))$pdo->exec('ALTER TABLE practice_areas ADD COLUMN '.$column.' '.$definition);
    }

    $statement=$pdo->prepare('SELECT field_config_json FROM admin_resources WHERE resource_key = :resource_key LIMIT 1');
    $statement->execute(['resource_key'=>'practice-areas']);
    $json=$statement->fetchColumn();
    $config=[];

    if(is_string($json)&&$json!==''){
        try{$decoded=json_decode($json,true,512,JSON_THROW_ON_ERROR);$config=is_array($decoded)?$decoded:[];}
        catch(\JsonException){$config=[];}
    }

    foreach(['excerpt','body','approach_body','cta_body'] as $field){
        $config[$field]=[...((array)($config[$field]??[])),'type'=>'richtext'];
    }

    $update=$pdo->prepare('UPDATE admin_resources SET field_config_json = :config WHERE resource_key = :resource_key');
    $update->execute(['resource_key'=>'practice-areas','config'=>json_encode($config,JSON_THROW_ON_ERROR)]);

    $pdo->exec('UPDATE practice_areas
        SET overview_heading = COALESCE(NULLIF(overview_heading, ""), "Strategic legal support for your business"),
            approach_heading = COALESCE(NULLIF(approach_heading, ""), "Practical, commercially minded counsel"),
            cta_heading = COALESCE(NULLIF(cta_heading, ""), "Let us help you navigate the next step")');
};
