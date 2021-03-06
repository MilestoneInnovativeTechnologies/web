<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAndUpdateCurrencyInCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->string("currency",5)->after("phonecode")->nullable();
        });
			
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'AD'");
			DB::statement("UPDATE countries SET `currency` = 'AED' WHERE `sortname` = 'AE'");
			DB::statement("UPDATE countries SET `currency` = 'AFN' WHERE `sortname` = 'AF'");
			DB::statement("UPDATE countries SET `currency` = 'XCD' WHERE `sortname` = 'AG'");
			DB::statement("UPDATE countries SET `currency` = 'XCD' WHERE `sortname` = 'AI'");
			DB::statement("UPDATE countries SET `currency` = 'ALL' WHERE `sortname` = 'AL'");
			DB::statement("UPDATE countries SET `currency` = 'AMD' WHERE `sortname` = 'AM'");
			DB::statement("UPDATE countries SET `currency` = 'AOA' WHERE `sortname` = 'AO'");
			DB::statement("UPDATE countries SET `currency` = 'ARS' WHERE `sortname` = 'AR'");
			DB::statement("UPDATE countries SET `currency` = 'USD' WHERE `sortname` = 'AS'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'AT'");
			DB::statement("UPDATE countries SET `currency` = 'AUD' WHERE `sortname` = 'AU'");
			DB::statement("UPDATE countries SET `currency` = 'AWG' WHERE `sortname` = 'AW'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'AX'");
			DB::statement("UPDATE countries SET `currency` = 'AZN' WHERE `sortname` = 'AZ'");
			DB::statement("UPDATE countries SET `currency` = 'BAM' WHERE `sortname` = 'BA'");
			DB::statement("UPDATE countries SET `currency` = 'BBD' WHERE `sortname` = 'BB'");
			DB::statement("UPDATE countries SET `currency` = 'BDT' WHERE `sortname` = 'BD'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'BE'");
			DB::statement("UPDATE countries SET `currency` = 'XOF' WHERE `sortname` = 'BF'");
			DB::statement("UPDATE countries SET `currency` = 'BGN' WHERE `sortname` = 'BG'");
			DB::statement("UPDATE countries SET `currency` = 'BHD' WHERE `sortname` = 'BH'");
			DB::statement("UPDATE countries SET `currency` = 'BIF' WHERE `sortname` = 'BI'");
			DB::statement("UPDATE countries SET `currency` = 'XOF' WHERE `sortname` = 'BJ'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'BL'");
			DB::statement("UPDATE countries SET `currency` = 'BMD' WHERE `sortname` = 'BM'");
			DB::statement("UPDATE countries SET `currency` = 'BND' WHERE `sortname` = 'BN'");
			DB::statement("UPDATE countries SET `currency` = 'BOB' WHERE `sortname` = 'BO'");
			DB::statement("UPDATE countries SET `currency` = 'USD' WHERE `sortname` = 'BQ'");
			DB::statement("UPDATE countries SET `currency` = 'BRL' WHERE `sortname` = 'BR'");
			DB::statement("UPDATE countries SET `currency` = 'BSD' WHERE `sortname` = 'BS'");
			DB::statement("UPDATE countries SET `currency` = 'BTN' WHERE `sortname` = 'BT'");
			DB::statement("UPDATE countries SET `currency` = 'NOK' WHERE `sortname` = 'BV'");
			DB::statement("UPDATE countries SET `currency` = 'BWP' WHERE `sortname` = 'BW'");
			DB::statement("UPDATE countries SET `currency` = 'BYN' WHERE `sortname` = 'BY'");
			DB::statement("UPDATE countries SET `currency` = 'BZD' WHERE `sortname` = 'BZ'");
			DB::statement("UPDATE countries SET `currency` = 'CAD' WHERE `sortname` = 'CA'");
			DB::statement("UPDATE countries SET `currency` = 'AUD' WHERE `sortname` = 'CC'");
			DB::statement("UPDATE countries SET `currency` = 'CDF' WHERE `sortname` = 'CD'");
			DB::statement("UPDATE countries SET `currency` = 'XAF' WHERE `sortname` = 'CF'");
			DB::statement("UPDATE countries SET `currency` = 'XAF' WHERE `sortname` = 'CG'");
			DB::statement("UPDATE countries SET `currency` = 'CHF' WHERE `sortname` = 'CH'");
			DB::statement("UPDATE countries SET `currency` = 'XOF' WHERE `sortname` = 'CI'");
			DB::statement("UPDATE countries SET `currency` = 'NZD' WHERE `sortname` = 'CK'");
			DB::statement("UPDATE countries SET `currency` = 'CLP' WHERE `sortname` = 'CL'");
			DB::statement("UPDATE countries SET `currency` = 'XAF' WHERE `sortname` = 'CM'");
			DB::statement("UPDATE countries SET `currency` = 'CNY' WHERE `sortname` = 'CN'");
			DB::statement("UPDATE countries SET `currency` = 'COP' WHERE `sortname` = 'CO'");
			DB::statement("UPDATE countries SET `currency` = 'CRC' WHERE `sortname` = 'CR'");
			DB::statement("UPDATE countries SET `currency` = 'CUP' WHERE `sortname` = 'CU'");
			DB::statement("UPDATE countries SET `currency` = 'CVE' WHERE `sortname` = 'CV'");
			DB::statement("UPDATE countries SET `currency` = 'ANG' WHERE `sortname` = 'CW'");
			DB::statement("UPDATE countries SET `currency` = 'AUD' WHERE `sortname` = 'CX'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'CY'");
			DB::statement("UPDATE countries SET `currency` = 'CZK' WHERE `sortname` = 'CZ'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'DE'");
			DB::statement("UPDATE countries SET `currency` = 'DJF' WHERE `sortname` = 'DJ'");
			DB::statement("UPDATE countries SET `currency` = 'DKK' WHERE `sortname` = 'DK'");
			DB::statement("UPDATE countries SET `currency` = 'XCD' WHERE `sortname` = 'DM'");
			DB::statement("UPDATE countries SET `currency` = 'DOP' WHERE `sortname` = 'DO'");
			DB::statement("UPDATE countries SET `currency` = 'DZD' WHERE `sortname` = 'DZ'");
			DB::statement("UPDATE countries SET `currency` = 'USD' WHERE `sortname` = 'EC'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'EE'");
			DB::statement("UPDATE countries SET `currency` = 'EGP' WHERE `sortname` = 'EG'");
			DB::statement("UPDATE countries SET `currency` = 'MAD' WHERE `sortname` = 'EH'");
			DB::statement("UPDATE countries SET `currency` = 'ERN' WHERE `sortname` = 'ER'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'ES'");
			DB::statement("UPDATE countries SET `currency` = 'ETB' WHERE `sortname` = 'ET'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'FI'");
			DB::statement("UPDATE countries SET `currency` = 'FJD' WHERE `sortname` = 'FJ'");
			DB::statement("UPDATE countries SET `currency` = 'FKP' WHERE `sortname` = 'FK'");
			DB::statement("UPDATE countries SET `currency` = 'USD' WHERE `sortname` = 'FM'");
			DB::statement("UPDATE countries SET `currency` = 'DKK' WHERE `sortname` = 'FO'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'FR'");
			DB::statement("UPDATE countries SET `currency` = 'XAF' WHERE `sortname` = 'GA'");
			DB::statement("UPDATE countries SET `currency` = 'GBP' WHERE `sortname` = 'GB'");
			DB::statement("UPDATE countries SET `currency` = 'XCD' WHERE `sortname` = 'GD'");
			DB::statement("UPDATE countries SET `currency` = 'GEL' WHERE `sortname` = 'GE'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'GF'");
			DB::statement("UPDATE countries SET `currency` = 'GBP' WHERE `sortname` = 'GG'");
			DB::statement("UPDATE countries SET `currency` = 'GHS' WHERE `sortname` = 'GH'");
			DB::statement("UPDATE countries SET `currency` = 'GIP' WHERE `sortname` = 'GI'");
			DB::statement("UPDATE countries SET `currency` = 'DKK' WHERE `sortname` = 'GL'");
			DB::statement("UPDATE countries SET `currency` = 'GMD' WHERE `sortname` = 'GM'");
			DB::statement("UPDATE countries SET `currency` = 'GNF' WHERE `sortname` = 'GN'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'GP'");
			DB::statement("UPDATE countries SET `currency` = 'XAF' WHERE `sortname` = 'GQ'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'GR'");
			DB::statement("UPDATE countries SET `currency` = 'GBP' WHERE `sortname` = 'GS'");
			DB::statement("UPDATE countries SET `currency` = 'GTQ' WHERE `sortname` = 'GT'");
			DB::statement("UPDATE countries SET `currency` = 'USD' WHERE `sortname` = 'GU'");
			DB::statement("UPDATE countries SET `currency` = 'XOF' WHERE `sortname` = 'GW'");
			DB::statement("UPDATE countries SET `currency` = 'GYD' WHERE `sortname` = 'GY'");
			DB::statement("UPDATE countries SET `currency` = 'HKD' WHERE `sortname` = 'HK'");
			DB::statement("UPDATE countries SET `currency` = 'AUD' WHERE `sortname` = 'HM'");
			DB::statement("UPDATE countries SET `currency` = 'HNL' WHERE `sortname` = 'HN'");
			DB::statement("UPDATE countries SET `currency` = 'HRK' WHERE `sortname` = 'HR'");
			DB::statement("UPDATE countries SET `currency` = 'HTG' WHERE `sortname` = 'HT'");
			DB::statement("UPDATE countries SET `currency` = 'HUF' WHERE `sortname` = 'HU'");
			DB::statement("UPDATE countries SET `currency` = 'IDR' WHERE `sortname` = 'ID'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'IE'");
			DB::statement("UPDATE countries SET `currency` = 'ILS' WHERE `sortname` = 'IL'");
			DB::statement("UPDATE countries SET `currency` = 'GBP' WHERE `sortname` = 'IM'");
			DB::statement("UPDATE countries SET `currency` = 'INR' WHERE `sortname` = 'IN'");
			DB::statement("UPDATE countries SET `currency` = 'USD' WHERE `sortname` = 'IO'");
			DB::statement("UPDATE countries SET `currency` = 'IQD' WHERE `sortname` = 'IQ'");
			DB::statement("UPDATE countries SET `currency` = 'IRR' WHERE `sortname` = 'IR'");
			DB::statement("UPDATE countries SET `currency` = 'ISK' WHERE `sortname` = 'IS'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'IT'");
			DB::statement("UPDATE countries SET `currency` = 'GBP' WHERE `sortname` = 'JE'");
			DB::statement("UPDATE countries SET `currency` = 'JMD' WHERE `sortname` = 'JM'");
			DB::statement("UPDATE countries SET `currency` = 'JOD' WHERE `sortname` = 'JO'");
			DB::statement("UPDATE countries SET `currency` = 'JPY' WHERE `sortname` = 'JP'");
			DB::statement("UPDATE countries SET `currency` = 'KES' WHERE `sortname` = 'KE'");
			DB::statement("UPDATE countries SET `currency` = 'KGS' WHERE `sortname` = 'KG'");
			DB::statement("UPDATE countries SET `currency` = 'KHR' WHERE `sortname` = 'KH'");
			DB::statement("UPDATE countries SET `currency` = 'AUD' WHERE `sortname` = 'KI'");
			DB::statement("UPDATE countries SET `currency` = 'KMF' WHERE `sortname` = 'KM'");
			DB::statement("UPDATE countries SET `currency` = 'XCD' WHERE `sortname` = 'KN'");
			DB::statement("UPDATE countries SET `currency` = 'KPW' WHERE `sortname` = 'KP'");
			DB::statement("UPDATE countries SET `currency` = 'KRW' WHERE `sortname` = 'KR'");
			DB::statement("UPDATE countries SET `currency` = 'KWD' WHERE `sortname` = 'KW'");
			DB::statement("UPDATE countries SET `currency` = 'KYD' WHERE `sortname` = 'KY'");
			DB::statement("UPDATE countries SET `currency` = 'KZT' WHERE `sortname` = 'KZ'");
			DB::statement("UPDATE countries SET `currency` = 'LAK' WHERE `sortname` = 'LA'");
			DB::statement("UPDATE countries SET `currency` = 'LBP' WHERE `sortname` = 'LB'");
			DB::statement("UPDATE countries SET `currency` = 'XCD' WHERE `sortname` = 'LC'");
			DB::statement("UPDATE countries SET `currency` = 'CHF' WHERE `sortname` = 'LI'");
			DB::statement("UPDATE countries SET `currency` = 'LKR' WHERE `sortname` = 'LK'");
			DB::statement("UPDATE countries SET `currency` = 'LRD' WHERE `sortname` = 'LR'");
			DB::statement("UPDATE countries SET `currency` = 'LSL' WHERE `sortname` = 'LS'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'LT'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'LU'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'LV'");
			DB::statement("UPDATE countries SET `currency` = 'LYD' WHERE `sortname` = 'LY'");
			DB::statement("UPDATE countries SET `currency` = 'MAD' WHERE `sortname` = 'MA'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'MC'");
			DB::statement("UPDATE countries SET `currency` = 'MDL' WHERE `sortname` = 'MD'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'ME'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'MF'");
			DB::statement("UPDATE countries SET `currency` = 'MGA' WHERE `sortname` = 'MG'");
			DB::statement("UPDATE countries SET `currency` = 'USD' WHERE `sortname` = 'MH'");
			DB::statement("UPDATE countries SET `currency` = 'MKD' WHERE `sortname` = 'MK'");
			DB::statement("UPDATE countries SET `currency` = 'XOF' WHERE `sortname` = 'ML'");
			DB::statement("UPDATE countries SET `currency` = 'MMK' WHERE `sortname` = 'MM'");
			DB::statement("UPDATE countries SET `currency` = 'MNT' WHERE `sortname` = 'MN'");
			DB::statement("UPDATE countries SET `currency` = 'MOP' WHERE `sortname` = 'MO'");
			DB::statement("UPDATE countries SET `currency` = 'USD' WHERE `sortname` = 'MP'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'MQ'");
			DB::statement("UPDATE countries SET `currency` = 'MRO' WHERE `sortname` = 'MR'");
			DB::statement("UPDATE countries SET `currency` = 'XCD' WHERE `sortname` = 'MS'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'MT'");
			DB::statement("UPDATE countries SET `currency` = 'MUR' WHERE `sortname` = 'MU'");
			DB::statement("UPDATE countries SET `currency` = 'MVR' WHERE `sortname` = 'MV'");
			DB::statement("UPDATE countries SET `currency` = 'MWK' WHERE `sortname` = 'MW'");
			DB::statement("UPDATE countries SET `currency` = 'MXN' WHERE `sortname` = 'MX'");
			DB::statement("UPDATE countries SET `currency` = 'MYR' WHERE `sortname` = 'MY'");
			DB::statement("UPDATE countries SET `currency` = 'MZN' WHERE `sortname` = 'MZ'");
			DB::statement("UPDATE countries SET `currency` = 'NAD' WHERE `sortname` = 'NA'");
			DB::statement("UPDATE countries SET `currency` = 'XPF' WHERE `sortname` = 'NC'");
			DB::statement("UPDATE countries SET `currency` = 'XOF' WHERE `sortname` = 'NE'");
			DB::statement("UPDATE countries SET `currency` = 'AUD' WHERE `sortname` = 'NF'");
			DB::statement("UPDATE countries SET `currency` = 'NGN' WHERE `sortname` = 'NG'");
			DB::statement("UPDATE countries SET `currency` = 'NIO' WHERE `sortname` = 'NI'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'NL'");
			DB::statement("UPDATE countries SET `currency` = 'NOK' WHERE `sortname` = 'NO'");
			DB::statement("UPDATE countries SET `currency` = 'NPR' WHERE `sortname` = 'NP'");
			DB::statement("UPDATE countries SET `currency` = 'AUD' WHERE `sortname` = 'NR'");
			DB::statement("UPDATE countries SET `currency` = 'NZD' WHERE `sortname` = 'NU'");
			DB::statement("UPDATE countries SET `currency` = 'NZD' WHERE `sortname` = 'NZ'");
			DB::statement("UPDATE countries SET `currency` = 'OMR' WHERE `sortname` = 'OM'");
			DB::statement("UPDATE countries SET `currency` = 'PAB' WHERE `sortname` = 'PA'");
			DB::statement("UPDATE countries SET `currency` = 'PEN' WHERE `sortname` = 'PE'");
			DB::statement("UPDATE countries SET `currency` = 'XPF' WHERE `sortname` = 'PF'");
			DB::statement("UPDATE countries SET `currency` = 'PGK' WHERE `sortname` = 'PG'");
			DB::statement("UPDATE countries SET `currency` = 'PHP' WHERE `sortname` = 'PH'");
			DB::statement("UPDATE countries SET `currency` = 'PKR' WHERE `sortname` = 'PK'");
			DB::statement("UPDATE countries SET `currency` = 'PLN' WHERE `sortname` = 'PL'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'PM'");
			DB::statement("UPDATE countries SET `currency` = 'NZD' WHERE `sortname` = 'PN'");
			DB::statement("UPDATE countries SET `currency` = 'USD' WHERE `sortname` = 'PR'");
			DB::statement("UPDATE countries SET `currency` = 'ILS' WHERE `sortname` = 'PS'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'PT'");
			DB::statement("UPDATE countries SET `currency` = 'USD' WHERE `sortname` = 'PW'");
			DB::statement("UPDATE countries SET `currency` = 'PYG' WHERE `sortname` = 'PY'");
			DB::statement("UPDATE countries SET `currency` = 'QAR' WHERE `sortname` = 'QA'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'RE'");
			DB::statement("UPDATE countries SET `currency` = 'RON' WHERE `sortname` = 'RO'");
			DB::statement("UPDATE countries SET `currency` = 'RSD' WHERE `sortname` = 'RS'");
			DB::statement("UPDATE countries SET `currency` = 'RUB' WHERE `sortname` = 'RU'");
			DB::statement("UPDATE countries SET `currency` = 'RWF' WHERE `sortname` = 'RW'");
			DB::statement("UPDATE countries SET `currency` = 'SAR' WHERE `sortname` = 'SA'");
			DB::statement("UPDATE countries SET `currency` = 'SBD' WHERE `sortname` = 'SB'");
			DB::statement("UPDATE countries SET `currency` = 'SCR' WHERE `sortname` = 'SC'");
			DB::statement("UPDATE countries SET `currency` = 'SDG' WHERE `sortname` = 'SD'");
			DB::statement("UPDATE countries SET `currency` = 'SEK' WHERE `sortname` = 'SE'");
			DB::statement("UPDATE countries SET `currency` = 'SGD' WHERE `sortname` = 'SG'");
			DB::statement("UPDATE countries SET `currency` = 'SHP' WHERE `sortname` = 'SH'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'SI'");
			DB::statement("UPDATE countries SET `currency` = 'NOK' WHERE `sortname` = 'SJ'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'SK'");
			DB::statement("UPDATE countries SET `currency` = 'SLL' WHERE `sortname` = 'SL'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'SM'");
			DB::statement("UPDATE countries SET `currency` = 'XOF' WHERE `sortname` = 'SN'");
			DB::statement("UPDATE countries SET `currency` = 'SOS' WHERE `sortname` = 'SO'");
			DB::statement("UPDATE countries SET `currency` = 'SRD' WHERE `sortname` = 'SR'");
			DB::statement("UPDATE countries SET `currency` = 'SSP' WHERE `sortname` = 'SS'");
			DB::statement("UPDATE countries SET `currency` = 'STD' WHERE `sortname` = 'ST'");
			DB::statement("UPDATE countries SET `currency` = 'USD' WHERE `sortname` = 'SV'");
			DB::statement("UPDATE countries SET `currency` = 'ANG' WHERE `sortname` = 'SX'");
			DB::statement("UPDATE countries SET `currency` = 'SYP' WHERE `sortname` = 'SY'");
			DB::statement("UPDATE countries SET `currency` = 'SZL' WHERE `sortname` = 'SZ'");
			DB::statement("UPDATE countries SET `currency` = 'USD' WHERE `sortname` = 'TC'");
			DB::statement("UPDATE countries SET `currency` = 'XAF' WHERE `sortname` = 'TD'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'TF'");
			DB::statement("UPDATE countries SET `currency` = 'XOF' WHERE `sortname` = 'TG'");
			DB::statement("UPDATE countries SET `currency` = 'THB' WHERE `sortname` = 'TH'");
			DB::statement("UPDATE countries SET `currency` = 'TJS' WHERE `sortname` = 'TJ'");
			DB::statement("UPDATE countries SET `currency` = 'NZD' WHERE `sortname` = 'TK'");
			DB::statement("UPDATE countries SET `currency` = 'USD' WHERE `sortname` = 'TL'");
			DB::statement("UPDATE countries SET `currency` = 'TMT' WHERE `sortname` = 'TM'");
			DB::statement("UPDATE countries SET `currency` = 'TND' WHERE `sortname` = 'TN'");
			DB::statement("UPDATE countries SET `currency` = 'TOP' WHERE `sortname` = 'TO'");
			DB::statement("UPDATE countries SET `currency` = 'TRY' WHERE `sortname` = 'TR'");
			DB::statement("UPDATE countries SET `currency` = 'TTD' WHERE `sortname` = 'TT'");
			DB::statement("UPDATE countries SET `currency` = 'AUD' WHERE `sortname` = 'TV'");
			DB::statement("UPDATE countries SET `currency` = 'TWD' WHERE `sortname` = 'TW'");
			DB::statement("UPDATE countries SET `currency` = 'TZS' WHERE `sortname` = 'TZ'");
			DB::statement("UPDATE countries SET `currency` = 'UAH' WHERE `sortname` = 'UA'");
			DB::statement("UPDATE countries SET `currency` = 'UGX' WHERE `sortname` = 'UG'");
			DB::statement("UPDATE countries SET `currency` = 'USD' WHERE `sortname` = 'UM'");
			DB::statement("UPDATE countries SET `currency` = 'USD' WHERE `sortname` = 'US'");
			DB::statement("UPDATE countries SET `currency` = 'UYU' WHERE `sortname` = 'UY'");
			DB::statement("UPDATE countries SET `currency` = 'UZS' WHERE `sortname` = 'UZ'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'VA'");
			DB::statement("UPDATE countries SET `currency` = 'XCD' WHERE `sortname` = 'VC'");
			DB::statement("UPDATE countries SET `currency` = 'VEF' WHERE `sortname` = 'VE'");
			DB::statement("UPDATE countries SET `currency` = 'USD' WHERE `sortname` = 'VG'");
			DB::statement("UPDATE countries SET `currency` = 'USD' WHERE `sortname` = 'VI'");
			DB::statement("UPDATE countries SET `currency` = 'VND' WHERE `sortname` = 'VN'");
			DB::statement("UPDATE countries SET `currency` = 'VUV' WHERE `sortname` = 'VU'");
			DB::statement("UPDATE countries SET `currency` = 'XPF' WHERE `sortname` = 'WF'");
			DB::statement("UPDATE countries SET `currency` = 'WST' WHERE `sortname` = 'WS'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'XK'");
			DB::statement("UPDATE countries SET `currency` = 'YER' WHERE `sortname` = 'YE'");
			DB::statement("UPDATE countries SET `currency` = 'EUR' WHERE `sortname` = 'YT'");
			DB::statement("UPDATE countries SET `currency` = 'ZAR' WHERE `sortname` = 'ZA'");
			DB::statement("UPDATE countries SET `currency` = 'ZMW' WHERE `sortname` = 'ZM'");
			DB::statement("UPDATE countries SET `currency` = 'ZWL' WHERE `sortname` = 'ZW'");
			
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn("currency");
        });
    }
}
