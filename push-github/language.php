<?php
// language.php - File helper untuk menangani bahasa

class Language {
    private $translations = [];
    private $lang = 'id';
    
    public function __construct() {
        // Menentukan bahasa dari GET, session, atau cookie
        $this->lang = isset($_GET['lang']) ? $_GET['lang'] : 
                    (isset($_SESSION['lang']) ? $_SESSION['lang'] : 
                    (isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'id'));
        
        // Validasi bahasa yang tersedia
        if (!in_array($this->lang, ['id', 'en', 'jp'])) {
            $this->lang = 'id';
        }
        
        // Menyimpan pilihan bahasa ke session dan cookie
        $_SESSION['lang'] = $this->lang;
        setcookie('lang', $this->lang, time() + (86400 * 30), "/"); // 30 hari
        
        // Memuat file terjemahan
        $this->loadTranslations();
    }
    
    private function loadTranslations() {
        $file = dirname(__FILE__) . "/locales/{$this->lang}.json";
        if (file_exists($file)) {
            $json = file_get_contents($file);
            $this->translations = json_decode($json, true);
        }
    }
    
    public function get($key) {
        // Support untuk nested keys seperti "nav.home"
        $keys = explode('.', $key);
        $value = $this->translations;
        
        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $key; // Mengembalikan key jika terjemahan tidak ditemukan
            }
        }
        
        return $value;
    }
    
    public function getCurrentLang() {
        return $this->lang;
    }
}

// Inisialisasi
$lang = new Language();

// Alias fungsi untuk memudahkan penggunaan
function __($key) {
    global $lang;
    return $lang->get($key);
}
?>