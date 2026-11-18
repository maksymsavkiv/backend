<?php
require_once __DIR__.'/BaseController.php';
require_once __DIR__.'/../../CsvRepository.php';
class RecordsController extends BaseController {
    private function repo(string $type): CsvRepository {return new CsvRepository($type);}
    public function list(string $type): never {
        $q=trim((string)($_GET['q']??''));
        if (strlen($q) > 150) {
            $this->json(['error' => 'Занадто довгий пошук'], 422);
        }
        $items=[];
        foreach($this->repo($type)->search($q) as $item)
        $items[]=array_merge($item->getAsAssocArray(),['id'=>$item->getId()]);
        $this->json($items);
    }
    public function change(string $type): never {
        $this->requireAdmin();$data=$this->input();$action=$data['action']??'add';
        if(!in_array($action,['add','update','delete'],true))$this->json(['error'=>'Невідома дія'],400);
        $repo=$this->repo($type);$id=filter_var($data['id']??null,FILTER_VALIDATE_INT);
        $old=null;
        if($action!=='add'){
            $old=$id>0?$repo->find((int)$id):null;
            if(!$old)$this->json(['error'=>'Запис не знайдено'],404);
        }
        if($action==='delete'){
            if($type==='categories')foreach($this->repo('properties')->all() as $p)
            if((int)$p->getCategory()===(int)$id)$this->json(['error'=>'Категорія містить властивості'],409);
            $repo->delete((int)$id);
            if($type==='examples' && $old->getImage())$this->removeImage($old->getImage());
            $this->json(['id'=>(int)$id]);
        }
    $name=trim((string)($data['name']??''));$description=trim((string)($data['description']??''));
    $code=trim((string)($data['code']??''));
    if($type==='categories'){
        if($name==='')$this->json(['error'=>'Вкажіть назву'],422);
        $record=['name'=>$name];
    }
    elseif($type==='properties'){
        $category=filter_var($data['category']??null,FILTER_VALIDATE_INT);
        if($name===''||$description===''||!$category||!$this->repo('categories')->find((int)$category))
        $this->json(['error'=>'Вкажіть назву, опис і категорію'],422);
        $record=['name'=>$name,'description'=>$description,'category'=>(int)$category];
    }
    else{
        if($description===''||$code==='')$this->json(['error'=>'Вкажіть опис та код'],422);
        $record=['description'=>$description,'code'=>$code];
    }
    if($action==='add')$this->json(['id'=>$repo->add($record)],201);
    $repo->update((int)$id,$record);$this->json(['id'=>(int)$id]);
    }
    private function removeImage(string $image):void {
        $root=realpath(__DIR__.'/../../uploads');$file=realpath(__DIR__.'/../../'.$image);
        if($root && $file && str_starts_with($file,$root.DIRECTORY_SEPARATOR) && is_file($file))unlink($file);
    }
    public function upload(): never {
        $this->requireAdmin();$id=filter_var($_POST['id']??null,FILTER_VALIDATE_INT);
        $repo=$this->repo('examples');$old=$id>0?$repo->find((int)$id):null;
        if(!$old)$this->json(['error'=>'Приклад не знайдено'],404);
        $file=$_FILES['image']??null;
        if(!$file||$file['error']!==UPLOAD_ERR_OK||!is_uploaded_file($file['tmp_name'])||$file['size']>3*1024*1024)
        $this->json(['error'=>'Некоректний файл (максимум 3 МБ)'],422);
        $allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
        $mime=(new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if(!isset($allowed[$mime])||!getimagesize($file['tmp_name']))$this->json(['error'=>'Тільки JPG, PNG, WebP'],422);
        $dir=__DIR__.'/../../uploads';if(!is_dir($dir))mkdir($dir,0755,true);
        $name=bin2hex(random_bytes(16)).'.'.$allowed[$mime];$path=$dir.'/'.$name;
        if(!move_uploaded_file($file['tmp_name'],$path))$this->json(['error'=>'Не вдалося зберегти файл'],500);
        $image='uploads/'.$name;
        try{$repo->update((int)$id,['image'=>$image]);}
        catch(Throwable $e){unlink($path);throw $e;}
        if($old->getImage())$this->removeImage($old->getImage());
        $this->json(['id'=>(int)$id,'image'=>$image]);
    }
}
