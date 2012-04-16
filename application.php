<?php
class Application {
    /**
     * @var Request
     */
    protected $_request;

    /**
     * @var Application
     */
    protected static $_instance = null;

    /**
     * @var array пути
     */
    protected static $_paths = array();

    /**
     * @var array
     */
    protected static $_folders = array('classes');

    /**
     * Создание запроса
     *
     * @param Request $request
     * @return Application
     */
    public function create($request) {
        $this->_request = $request;
        
        return $this;
    }

    /**
     * Включение библиотеки с помощью
     * файла её конфигурации
     *
     * @param string $lib
     * @param string $libFile
     */
    public function includeLib($lib, $libFile)
    {
        require_once self::getLibPath($lib) . $libFile;
    }

    /**
     * Получить путь до библиотеки
     * Не обязательно чтобы она существовала
     *
     * @static
     * @param $lib
     * @return string
     */
    public static function getLibPath($lib)
    {
        return ROOT_PATH . 'library' . DIRECTORY_SEPARATOR .
            $lib . DIRECTORY_SEPARATOR;
    }

    /**
     * Регистрация библиотеки
     *
     * @param string $lib
     * @param bool $init
     */
    public function registyLib($lib, $init = FALSE) {
        $libPath = self::getLibPath($lib);

        self::registryAutoload($libPath);

        if ($init != FALSE)
            $this->includeLib($lib, $init);
    }

    /**
     * Регистрация директории
     *
     * @param string $directory
     */
    public function registryAutoload($directory) {
        self::$_paths[] = $directory;
    }

    /**
     * @static
     * @param $fileName
     * @param $folder classes|config or else
     * @return string|FALSE
     */
    public static function findFile($fileName, $folder = FALSE)
    {
        if ($folder == FALSE)
            $searchDir = self::$_folders;
        else $searchDir = array($folder);

        foreach (self::$_paths as $path)
        {
            foreach ($searchDir as $folder)
            {
                $full = $path . $folder . DIRECTORY_SEPARATOR . $fileName;

                if (file_exists($full) != FALSE)
                    return $full;
            }
        }

        return FALSE;
    }

    public static function autoLoad($className) {
        $realName = str_replace('_', DIRECTORY_SEPARATOR, $className);
        $fileName = strtolower($realName) . '.php';

        if ($path = self::findFile($fileName))
        {
            require_once $path;
        }

        return FALSE;
    }

    /**
     * Init Application
     */
    protected function __construct() {
        spl_autoload_register('Application::autoLoad');
    }

    /**
     * @static
     * @return Application
     */
    public static function getInstance() {
        if(self::$_instance == null){
            self::$_instance = new Application();
        }
        
        return self::$_instance;
    }
    
    public function run() {
        $request = $this->_request;        
        $request->execute();
        $request->acceptHeaders();        
        return $request->getBody();
    }
}