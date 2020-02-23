<?php
/******************************************************************************/
//                                                                            //
//                           InstantCMS v1.10.6                               //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2015                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

class cmsUploadPhoto {

    private static $instance;

    public $upload_url    = '';			// относительные путь загрузки
	public $upload_dir    = '';			// директория загрузки (абсолютный)
	public $filename      = '';	        // имя файла
	public $small_size_w  = 96;	    	// ширина миниатюры
	public $small_size_h  = '';			// высота миниатюры
	public $medium_size_w = 480;		// ширина среднего изображения
	public $medium_size_h = '';			// высота среднего изображения
	public $thumbsqr      = true;		// квадратное изображение, да по умолчанию
	public $is_watermark  = true;		// накладывать ватермарк, да по умолчанию
	public $is_saveorig   = 0;			// сохранять оригинал фото, нет по умолчанию
	public $dir_small     = 'small/';	// директория загрузки миниатюры
	public $dir_medium    = 'medium/';	// директория загрузки среднего изображения
	public $only_medium   = false;		// загружать только среднее изображение, нет по умолчанию
	public $input_name    = 'Filedata';	// название поля загрузки файла

// ============================================================================ //
// ============================================================================ //

	private function __construct(){}

    private function __clone() {}

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

// ============================================================================ //
    /**
     * Загружает фото файл
     * @return array $file (filename, realfile)
     */
    public function uploadPhoto($old_file=''){

		// если каталог загрузки не определен, возвращаем ложь
		if (!$this->upload_dir) { return false; }

		if (!empty($_FILES[$this->input_name]['name'])){

			cmsCore::includeGraphics();

            $input_name = preg_replace('/[^a-zA-Zа-яёЁА-Я0-9\.\-_ ]/ui', '',
                                    mb_substr(basename(strval($_FILES[$this->input_name]['name'])), 0, 160));
            // расширение
            $ext = strtolower(pathinfo($input_name, PATHINFO_EXTENSION));
            // имя файла без расширения
            $realfile = str_replace('.'.$ext, '', $input_name);

			if (!$ext || !in_array($ext, array('jpg','jpeg','gif','png','bmp'), true)) { return false; }

			$this->filename 	   = $this->filename ? $this->filename : md5(time().$realfile).'.'.$ext;

			$uploadphoto 		   = $this->upload_dir . $this->filename;
			$uploadthumb['small']  = $this->upload_dir . $this->dir_small . $this->filename;
			$uploadthumb['medium'] = $this->upload_dir . $this->dir_medium . $this->filename;

			$source				   = $_FILES[$this->input_name]['tmp_name'];
			$errorCode			   = $_FILES[$this->input_name]['error'];

			if (cmsCore::moveUploadedFile($source, $uploadphoto, $errorCode)) {

				// удаляем предыдущий файл если необходимо
				$this->deletePhotoFile($old_file);

                if (!$this->isImage($uploadphoto)){
                    $this->deletePhotoFile($this->filename);
                    return false;
                }

				if (!$this->small_size_h) { $this->small_size_h = $this->small_size_w; }
				if (!$this->medium_size_h) { $this->medium_size_h = $this->medium_size_w; }

				// Гененрируем маленькое и среднее изображения
				if(!$this->only_medium){
                    if(!is_dir($this->upload_dir . $this->dir_small)) { @mkdir($this->upload_dir . $this->dir_small); }
					@img_resize($uploadphoto, $uploadthumb['small'], $this->small_size_w, $this->small_size_h, $this->thumbsqr);
				}
                if(!is_dir($this->upload_dir . $this->dir_medium)) { @mkdir($this->upload_dir . $this->dir_medium); }
				@img_resize($uploadphoto, $uploadthumb['medium'], $this->medium_size_w, $this->medium_size_h, false, false);

				// Накладывать ватермарк
				if($this->is_watermark) { @img_add_watermark($uploadthumb['medium']); }

				// сохранять оригинал
				if(!$this->is_saveorig) { @unlink($uploadphoto); } elseif($this->is_watermark) { @img_add_watermark($uploadphoto); }

				$file['filename'] = $this->filename;

				$file['realfile'] = $realfile;


			} else {

				return false;

			}


		} else {

			return false;

		}

        return $file;

    }
// ============================================================================ //
    public function isImage($src){

        $size = getimagesize($src);

        if ($size === false) return false;

        return true;

    }
// ============================================================================ //
    /**
     * Удаляет файл фото с папок загрузки
     * @return bool
     */
	public function deletePhotoFile($file=''){

		if (!($file && $this->upload_dir)) { return false; }

		@chmod($this->upload_dir . $file, 0777);
		@unlink($this->upload_dir . $file);
		@chmod($this->upload_dir . $this->dir_small . $file, 0777);
		@unlink($this->upload_dir . $this->dir_small . $file);
		@chmod($this->upload_dir . $this->dir_medium . $file, 0777);
		@unlink($this->upload_dir . $this->dir_medium . $file);

        return true;

    }

    /**
     * Загружает файл на сервер
     * @param string $post_filename Название поля с файлом в массиве $_FILES
     * @param string $allowed_ext Список допустимых расширений (через запятую)
     * @return array
     */
    public function upload($post_filename, $allowed_ext = false){

        if ($this->isUploadedXHR($post_filename)){
            return $this->uploadXHR($post_filename, $allowed_ext);
        }

        return array(
            'success' => false,
            'error' => 'UPLOAD_ERR_NO_FILE'
        );

    }
    
    public function isUploadedXHR($name){
        return isset($_GET['qqfile']);
    }
    
// ============================================================================ //
//============================================================================//

    /**
     * Загружает файл на сервер переданный через XHR
     * @param string $post_filename Название поля с файлом в массиве $_GET
     * @param string $allowed_ext Список допустимых расширений (через запятую)
     * @return array
     */
    public function uploadXHR($post_filename, $allowed_ext = false){

        $dest_name  = $this->files_sanitize_name($_GET['qqfile']);
        $dest_info  = pathinfo($dest_name);
        $dest_ext   = $dest_info['extension'];
        
        $dest_size  = 10;

        if ($allowed_ext !== false){
            $allowed_ext = explode(",", $allowed_ext);
            foreach($allowed_ext as $idx=>$ext){ $allowed_ext[$idx] = trim($ext); }
            if (!in_array($dest_ext, $allowed_ext)){
                return array(
                    'error' => 'UPLOAD_ERR_MIME',
                    'success' => false,
                    'name' => $dest_name
                );
            }
        }

        $this->filename 	   = $this->filename ? $this->filename : md5(time().$dest_info['basename']) . '.' . $dest_ext;

        $uploadphoto 	       = $this->upload_dir . $this->filename;
        $uploadthumb['small']  = $this->upload_dir . $this->dir_small . $this->filename;
        $uploadthumb['medium'] = $this->upload_dir . $this->dir_medium . $this->filename;

        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);

        if ($realSize != $this->getXHRFileSize()){
            return array(
                'success' => false,
                'error' => 'UPLOAD_ERR_PARTIAL',
                'name' => $dest_name,
                'path' => ''
            );
        }

        $target = fopen($uploadphoto, "w");
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);
        
        cmsCore::includeGraphics();
        
        if (!$this->small_size_h) { $this->small_size_h = $this->small_size_w; }
        if (!$this->medium_size_h) { $this->medium_size_h = $this->medium_size_w; }

        //Генерируем маленькое и среднее изображения
        if(!$this->only_medium){
            if(!is_dir($this->upload_dir . $this->dir_small)) { @mkdir($this->upload_dir . $this->dir_small); }
            @img_resize($uploadphoto, $uploadthumb['small'], $this->small_size_w, $this->small_size_h, $this->thumbsqr);
        }
        
        if(!is_dir($this->upload_dir . $this->dir_medium)) { @mkdir($this->upload_dir . $this->dir_medium); }
        @img_resize($uploadphoto, $uploadthumb['medium'], $this->medium_size_w, $this->medium_size_h, false, false);

        // Накладывать ватермарк
        if($this->is_watermark) { @img_add_watermark($uploadthumb['medium']); }

        // сохранять оригинал
        if(!$this->is_saveorig) { @unlink($uploadphoto); } elseif($this->is_watermark) { @img_add_watermark($uploadphoto); }

        return array(
            'success'  => true,
            'path'     => $uploadphoto,
            'url'      =>  $this->upload_url. $this->dir_small . $this->filename,
            'name'     => $dest_name,
            'size'     => $dest_size,
            'imageurl' => $this->filename
        );

    }

    public function getXHRFileSize(){
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];
        } else {
            return false;
        }
    }
    
    /**
     * Очищает имя файла от специальных символов
     *
     * @param string $filename
     * @return string
     */
    public function files_sanitize_name($filename){

        $filename = mb_strtolower($filename);
        $filename = preg_replace(array('/[\&]/', '/[\@]/', '/[\#]/'), array('-and-', '-at-', '-number-'), $filename);
        $filename = preg_replace('/[^(\x20-\x7F)]*/','', $filename);
        $filename = str_replace(' ', '-', $filename);
        $filename = str_replace('\'', '', $filename);
        $filename = preg_replace('/[^\w\-\.]+/', '', $filename);
        $filename = preg_replace('/[\-]+/', '-', $filename);

        return $filename;

    }

}
