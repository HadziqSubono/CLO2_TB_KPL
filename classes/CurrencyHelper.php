<?php
// classes/CurrencyHelper.php - Currency Formatting Helper
class CurrencyHelper {
    public function formatRupiah($amount) {
        if (!is_numeric($amount)) {
            return 'Rp 0';
        }
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    public function formatCurrency($amount, $currency = 'IDR') {
        if (!is_numeric($amount)) {
            return '0';
        }

        switch (strtoupper($currency)) {
            case 'IDR':
                return $this->formatRupiah($amount);
            case 'USD':
                return '$' . number_format($amount, 2);
            case 'EUR':
                return '€' . number_format($amount, 2);
            default:
                return number_format($amount, 2);
        }
    }

    public function parseCurrency($currencyString) {
        // Remove currency symbols and formatting
        $cleaned = preg_replace('/[^\d,.]/', '', $currencyString);
        $cleaned = str_replace(',', '', $cleaned);
        return floatval($cleaned);
    }
}
?>