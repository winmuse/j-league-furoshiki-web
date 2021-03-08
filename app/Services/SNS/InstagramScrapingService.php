<?php

/**
 * [OSX セットアップ方法]
 *
 * Chrome WebDriver
 * $ brew install chromedriver
 *
 * セキュリティ設定
 * [System Preferences] - [Security & Privacy] - [Accessibility]
 *   [Terminal] [Automator] を追加
 */

namespace App\Services\SNS;

use App\Models\ArticleInstagram;
use Facebook\Facebook;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use App\Models\Media;
use App\Models\InstagramCredential;
use App\Services\Image\ImageService;

class InstagramScrapingService
{
    private $fb;
    private $imgService;

    const FILE_SELECT_COMMAND = 'osascript upload.scpt';

    /**
     * @var ChromeDriver
     */
    protected $_driver;

    public function __construct(
        Facebook $fb,
        ImageService $imageService
    )
    {
        $this->fb = $fb;
        $this->imgService = $imageService;

        $this->_initializeChrome();
    }

    public function __destruct()
    {
        $this->_driver->close();
    }

    /**
     * post media to Instagram
     * @param InstagramCredential $igCredential
     * @param String $message
     * @param ArticleInstagram $articleIg
     * @return array['error' => error , 'id'=>containerId]
     * @throws
     */
    public function postMedia($igCredential, $message, $articleIg)
    {
        $res = $this->downloadMedia($articleIg);
        if ($res !== "1") return $res;

        // ログイン
        $username = $igCredential->account_name;
        $password = $igCredential->ig_password;

        $loginUrl = 'https://www.instagram.com/accounts/login/';
        $this->_driver->get($loginUrl);

        sleep(3);

        $this->_driver->findElement(WebDriverBy::cssSelector('input[name="username"]'))->sendKeys($username);

        sleep(1);

        $this->_driver->findElement(WebDriverBy::cssSelector('input[name="password"]'))->sendKeys($password);

        sleep(2);

        $this->_driver->findElement(WebDriverBy::cssSelector('button[type="submit"]'))->click();

        // パスワード保存
        sleep(5);
        try {
            $this->_driver->findElement(WebDriverBy::cssSelector('.yWX7d'));
            $pass = true;
        } catch (\Exception $e) {
            $pass = false;
        }
        if ($pass) {
            $this->_driver->findElement(WebDriverBy::cssSelector('.yWX7d'))->click();
        }

        // お知らせON
        sleep(5);
        try {
            $this->_driver->findElement(WebDriverBy::cssSelector('.HoLwm'));
            $notice = true;
        } catch (\Exception $e) {
            $notice = false;
        }
        if ($notice) {
            $this->_driver->findElement(WebDriverBy::cssSelector('.HoLwm'))->click();
        }

        // ホーム画面に追加するか
        sleep(5);
        try {
            $this->_driver->findElement(WebDriverBy::cssSelector('.HoLwm'));
            $home = true;
        } catch (\Exception $e) {
            $home = false;
        }
        if ($home) {
            $this->_driver->findElement(WebDriverBy::cssSelector('.HoLwm'))->click();
        }

        sleep(5);
        $this->_driver->findElement(WebDriverBy::cssSelector('.q02Nz'))->click();
        sleep(3);

        exec(self::FILE_SELECT_COMMAND);

        sleep(5);

        $this->_driver->findElement(WebDriverBy::cssSelector('button.pHnkA'))->click();

        sleep(3);

        $this->_driver->findElement(WebDriverBy::cssSelector('button.UP43G'))->click();

        sleep(3);

        $this->_driver->findElement(WebDriverBy::cssSelector('._472V_'))->sendKeys($message);

        sleep(1);

        $this->_driver->findElement(WebDriverBy::cssSelector('button.UP43G'))->click();

        sleep(5);

        return [];
    }

    /**
     * Chromeの初期設定
     */
    protected function _initializeChrome()
    {
        // ダウンロードしたchromedriverのパスを指定
        putenv('webdriver.chrome.driver=/usr/local/bin/chromedriver');

        // Chromeを起動するときのオプション指定用
        $options = new ChromeOptions();

        // ヘッドレスで起動するように指定
        // $options->addArguments(['--headless']);
        $options->setExperimentalOption('mobileEmulation', ['deviceName' => 'Nexus 5']);

        $caps = DesiredCapabilities::chrome();
        $caps->setCapability(ChromeOptions::CAPABILITY, $options);

        $this->_driver = ChromeDriver::start($caps);
    }

    /**
     * 投稿するメディアのダウンロード
     *
     * @param ArticleInstagram $articleIg
     * @return string 'path'
     * @throws
     */
    protected function downloadMedia(ArticleInstagram $articleIg): string
    {
        try {
            $save_path = '/Users/sinoue/Desktop/instagram.jpg';
            // $save_path = storage_path('instagram.jpg');
            $image = file_get_contents($articleIg->media_url);

            file_put_contents($save_path, $image);
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
