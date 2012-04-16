<?php

/**
 * Класс для загрузи файлов
 */
class Loader {
    /**
     * @var array исследуемый фаил
     */
    protected $_file;

    /**
     * @var array ошибки
     */
    protected $_errors = array();

    /**
     * @var bool прошла ли валидация
     */
    protected $_validate = TRUE;

    /**
     * @var string куда сохранен файл
     */
    protected $_path;

    /**
     * @var bool пуст ли файл
     */
    protected $_isEmpty = TRUE;

    /**
     * Указать файл для класса
     *
     * @static
     * @param $field
     * @return Loader
     */
    public static function file($field)
    {
        $loader = new Loader($field);

        return $loader;
    }

    /**
     * Получить расширение файла
     *
     * @return string
     */
    public function getExtension()
    {
        return mb_strtolower(pathinfo(Arr::get($this->_file, 'name'), PATHINFO_EXTENSION));
    }

    /**
     * Получить размер файла в килобайтах
     *
     * @return int
     */
    public function getSize()
    {
        return ceil(Arr::get($this->_file, 'size', 0) / 1024);
    }

    /**
     * Создать экземпляр класса
     *
     * @param string $field
     */
    public function __construct($field)
    {
        $this->_file = Arr::get($_FILES, $field, NULL);
        $this->_errors = array();
    }

    /**
     * Получить ошибки
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Проверка на валидность
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->_validate;
    }

    public function isLoaded()
    {
        return $this->_isEmpty == FALSE;
    }

    /**
     * Получить ошибку загрузки
     *
     * @return int
     */
    public function getFileError()
    {
        return Arr::get($this->_file, 'error', NULL);
    }

    /**
     * Получить имя файла, с расширением
     *
     * @return string
     */
    public function getFileName()
    {
        return Arr::get($this->_file, 'name', NULL);
    }

    /**
     * Файл не должен быть пустым
     *
     * @return Loader
     */
    public function notEmpty() {
        $this->_isEmpty = FALSE;

        if (($this->_file == FALSE) || ($this->getFileError() != FALSE))
        {
            $this->_errors[] = 'загрузите файл';
            $this->_validate = FALSE;
            $this->_isEmpty = TRUE;
        }

        return $this;
    }

    /**
     * Максимальный размер в килобайтах
     *
     * @param int $maxSize
     * @return Loader
     */
    public function maxSize($maxSize) {
        if ($this->_isEmpty == FALSE)
            if ($this->getSize() > $maxSize)
            {
                $this->_validate = FALSE;
                $this->_errors[] = 'размер файла не должен быть больше чем ' . $maxSize . ' кб';
            }

        return $this;
    }

    /**
     * Минимальный размер файла в килобайтах
     *
     * @param int $minSize
     * @return Loader
     */
    public function minSize($minSize) {
        if ($this->_isEmpty == FALSE)
            if ($this->getSize() < $minSize)
            {
                $this->_validate = FALSE;
                $this->_errors[] = 'размер файла не должен быть меньше чем ' . $minSize . ' кб';
            }

        return $this;
    }

    /**
     * Проверка на расширение файла
     *
     * @param array $extensions
     * @return Loader
     */
    public function isExtension($extensions) {
        if ($this->_isEmpty == FALSE)
        {
            $realExt = $this->getExtension();
            $supExt = FALSE;

            foreach ($extensions as $ext)
                if (mb_strtolower($ext) == $realExt)
                {
                    $supExt = TRUE;
                    break;
                }

            if ($supExt == FALSE)
            {
                $this->_validate = FALSE;
                $this->_errors[] = 'расширение файла должно быть ' . implode(', ', $extensions);
            }
        }

        return $this;
    }

    /**
     * Проверка. Загруженно ли изображение
     *
     * @return Loader
     */
    public function isImage() {
        $this->isExtension(array('jpg','jpeg','png','gif'));

        return $this;
    }

    /**
     * Весь путь к сохраненному файла
     *
     * @return string
     */
    public function getSaveFull()
    {
        return $this->_path;
    }

    /**
     * Только директория сохраненного файла
     *
     * @return string
     */
    public function getSavePath()
    {
        return pathinfo($this->_path, PATHINFO_DIRNAME);
    }

    /**
     * Только имя сохраненного файла
     *
     * @return string
     */
    public function getSaveFileName()
    {
        return pathinfo($this->_path, PATHINFO_BASENAME);
    }

    /**
     * Сохранить файл. Обязательно указать куда
     *
     * Можно указать имя файла без расширения
     * Можно указать расширение файла, или использовать
     * оригинальное
     *
     * Полный путь можно получить методот getSavePath()
     *
     * @param string $destination
     * @param string $fileName
     * @param string $ext
     * @return Loader
     */
    public function save($destination, $fileName = NULL, $ext = NULL)
    {
        if ($this->_validate)
        {
            $destination = realpath($destination) . DIRECTORY_SEPARATOR;
            $ext = ($ext == FALSE) ? $this->getExtension() : $ext;
            $fileName = ($fileName == FALSE) ? pathinfo($this->getFileName(), PATHINFO_FILENAME) : $fileName;
            $this->_path = $destination . $fileName .
                ($ext != FALSE ? '.' . $ext : NULL);

            if (is_writable($destination) == FALSE)
            {
                $this->_validate = FALSE;
                $this->_errors[] = 'директория ' . $destination . ' не имеет прав для записи';
            }
            else
                move_uploaded_file($this->_file['tmp_name'], $this->_path);
        }

        return $this;
    }

    /**
     * Удалить файл
     *
     * @param bool $path
     * @return Loader
     */
    public function removeFile($path = FALSE)
    {
        $path = ($path == FALSE) ? $this->getSavePath() : $path;

        if (($path != FALSE) && (is_file($path)))
            unlink($path);

        return $this;
    }
}