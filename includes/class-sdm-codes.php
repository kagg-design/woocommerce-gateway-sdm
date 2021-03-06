<?php
/**
 * WooCommerce SDM Bank Gateway Codes.
 *
 * Methods contain currency and error codes.
 *
 * @package wc-gateway-sdm
 */

/**
 * Class SDM_Codes
 */
class SDM_Codes {

	/**
	 * Get currency code
	 *
	 * @param string $currency 3 character currency name in WooCommerce.
	 *
	 * @return int
	 */
	public static function get_currency_code( $currency ) {
		$currency_codes = [
			'AED' => 784,
			'AFN' => 971,
			'ALL' => 8,
			'AMD' => 051,
			'ANG' => 532,
			'AOA' => 973,
			'ARS' => 032,
			'AUD' => 036,
			'AWG' => 533,
			'AZN' => 944,
			'BAM' => 977,
			'BBD' => 052,
			'BDT' => 050,
			'BGN' => 975,
			'BHD' => 48,
			'BIF' => 108,
			'BMD' => 060,
			'BND' => 96,
			'BOB' => 68,
			'BOV' => 984,
			'BRL' => 986,
			'BSD' => 044,
			'BTN' => 064,
			'BWP' => 072,
			'BYN' => 933,
			'BZD' => 84,
			'CAD' => 124,
			'CDF' => 976,
			'CHE' => 947,
			'CHF' => 756,
			'CHW' => 948,
			'CLF' => 990,
			'CLP' => 152,
			'CNY' => 156,
			'COP' => 170,
			'COU' => 970,
			'CRC' => 188,
			'CUC' => 931,
			'CUP' => 192,
			'CVE' => 132,
			'CZK' => 203,
			'DJF' => 262,
			'DKK' => 208,
			'DOP' => 214,
			'DZD' => 012,
			'EGP' => 818,
			'ERN' => 232,
			'ETB' => 230,
			'EUR' => 978,
			'FJD' => 242,
			'FKP' => 238,
			'GBP' => 826,
			'GEL' => 981,
			'GHS' => 936,
			'GIP' => 292,
			'GMD' => 270,
			'GNF' => 324,
			'GTQ' => 320,
			'GYD' => 328,
			'HKD' => 344,
			'HNL' => 340,
			'HRK' => 191,
			'HTG' => 332,
			'HUF' => 348,
			'IDR' => 360,
			'ILS' => 376,
			'INR' => 356,
			'IQD' => 368,
			'IRR' => 364,
			'ISK' => 352,
			'JMD' => 388,
			'JOD' => 400,
			'JPY' => 392,
			'KES' => 404,
			'KGS' => 417,
			'KHR' => 116,
			'KMF' => 174,
			'KPW' => 408,
			'KRW' => 410,
			'KWD' => 414,
			'KYD' => 136,
			'KZT' => 398,
			'LAK' => 418,
			'LBP' => 422,
			'LKR' => 144,
			'LRD' => 430,
			'LSL' => 426,
			'LYD' => 434,
			'MAD' => 504,
			'MDL' => 498,
			'MGA' => 969,
			'MKD' => 807,
			'MMK' => 104,
			'MNT' => 496,
			'MOP' => 446,
			'MRO' => 478,
			'MUR' => 480,
			'MVR' => 462,
			'MWK' => 454,
			'MXN' => 484,
			'MXV' => 979,
			'MYR' => 458,
			'MZN' => 943,
			'NAD' => 516,
			'NGN' => 566,
			'NIO' => 558,
			'NOK' => 578,
			'NPR' => 524,
			'NZD' => 554,
			'OMR' => 512,
			'PAB' => 590,
			'PEN' => 604,
			'PGK' => 598,
			'PHP' => 608,
			'PKR' => 586,
			'PLN' => 985,
			'PYG' => 600,
			'QAR' => 634,
			'RON' => 946,
			'RSD' => 941,
			'RUB' => 643,
			'RWF' => 646,
			'SAR' => 682,
			'SBD' => 90,
			'SCR' => 690,
			'SDG' => 938,
			'SEK' => 752,
			'SGD' => 702,
			'SHP' => 654,
			'SLL' => 694,
			'SOS' => 706,
			'SRD' => 968,
			'SSP' => 728,
			'STD' => 678,
			'SVC' => 222,
			'SYP' => 760,
			'SZL' => 748,
			'THB' => 764,
			'TJS' => 972,
			'TMT' => 934,
			'TND' => 788,
			'TOP' => 776,
			'TRY' => 949,
			'TTD' => 780,
			'TWD' => 901,
			'TZS' => 834,
			'UAH' => 980,
			'UGX' => 800,
			'USD' => 840,
			'USN' => 997,
			'UYI' => 940,
			'UYU' => 858,
			'UZS' => 860,
			'VEF' => 937,
			'VND' => 704,
			'VUV' => 548,
			'WST' => 882,
			'XAF' => 950,
			'XAG' => 961,
			'XAU' => 959,
			'XBA' => 955,
			'XBB' => 956,
			'XBC' => 957,
			'XBD' => 958,
			'XCD' => 951,
			'XDR' => 960,
			'XOF' => 952,
			'XPD' => 964,
			'XPF' => 953,
			'XPT' => 962,
			'XSU' => 994,
			'XTS' => 963,
			'XUA' => 965,
			'XXX' => 999,
			'YER' => 886,
			'ZAR' => 710,
			'ZMW' => 967,
			'ZWL' => 932,
		];

		return $currency_codes[ $currency ];
	}

	/**
	 * Get result message.
	 *
	 * @param string $result Result.
	 *
	 * @return mixed|string|void
	 */
	public static function get_result_message( $result ) {
		$result_messages = [
			'0' => __( 'Transaction completed successfully', 'woocommerce-gateway-sdm' ),
			'1' => __( 'Repeated transaction found', 'woocommerce-gateway-sdm' ),
			'2' => __( 'Transaction declined', 'woocommerce-gateway-sdm' ),
			'3' => __( 'Error processing transaction', 'woocommerce-gateway-sdm' ),
			'4' => __( 'Informational message', 'woocommerce-gateway-sdm' ),
		];
		if ( array_key_exists( $result, $result_messages ) ) {
			return $result_messages[ $result ];
		} else {
			return __( 'Unknown error', 'woocommerce-gateway-sdm' );
		}
	}

	/**
	 * Get detailed message.
	 *
	 * @param string $rc Detailed result code.
	 *
	 * @return mixed|string|void
	 */
	public static function get_detailed_message( $rc ) {
		$detailed_messages = [
			'00' => __( 'Successfully completed', 'woocommerce-gateway-sdm' ),
			'01' => __( 'Refer to card issuer', 'woocommerce-gateway-sdm' ),
			'02' => __( 'Refer to card issuer\'s special condition', 'woocommerce-gateway-sdm' ),
			'03' => __( 'Invalid merchant / source', 'woocommerce-gateway-sdm' ),
			'04' => __( 'PICK UP', 'woocommerce-gateway-sdm' ),
			'05' => __( 'Do not Honour', 'woocommerce-gateway-sdm' ),
			'06' => __( 'Error', 'woocommerce-gateway-sdm' ),
			'07' => __( 'Pick-up card, special condition', 'woocommerce-gateway-sdm' ),
			'08' => __( 'Honour with identification', 'woocommerce-gateway-sdm' ),
			'09' => __( 'Request in progress', 'woocommerce-gateway-sdm' ),
			'10' => __( 'Approved for partial amount', 'woocommerce-gateway-sdm' ),
			'11' => __( 'Approved (VIP)', 'woocommerce-gateway-sdm' ),
			'12' => __( 'Invalid transaction', 'woocommerce-gateway-sdm' ),
			'13' => __( 'Invalid amount', 'woocommerce-gateway-sdm' ),
			'14' => __( 'No such card', 'woocommerce-gateway-sdm' ),
			'15' => __( 'No such issuer', 'woocommerce-gateway-sdm' ),
			'16' => __( 'Approved, update track 3', 'woocommerce-gateway-sdm' ),
			'17' => __( 'Customer cancellation', 'woocommerce-gateway-sdm' ),
			'18' => __( 'Customer dispute', 'woocommerce-gateway-sdm' ),
			'19' => __( 'Re-enter transaction', 'woocommerce-gateway-sdm' ),
			'20' => __( 'Invalid response', 'woocommerce-gateway-sdm' ),
			'21' => __( 'No action taken', 'woocommerce-gateway-sdm' ),
			'22' => __( 'Suspected malfunction', 'woocommerce-gateway-sdm' ),
			'23' => __( 'Unacceptable transaction fee', 'woocommerce-gateway-sdm' ),
			'24' => __( 'File update not supported by receiver', 'woocommerce-gateway-sdm' ),
			'25' => __( 'No such record', 'woocommerce-gateway-sdm' ),
			'26' => __( 'Duplicate record update, old record replaced', 'woocommerce-gateway-sdm' ),
			'27' => __( 'File update field edit error', 'woocommerce-gateway-sdm' ),
			'28' => __( 'File locked out while update', 'woocommerce-gateway-sdm' ),
			'29' => __( 'File update error, contact acquirer', 'woocommerce-gateway-sdm' ),
			'30' => __( 'Format error', 'woocommerce-gateway-sdm' ),
			'31' => __( 'Issuer signed-off', 'woocommerce-gateway-sdm' ),
			'32' => __( 'Completed partially', 'woocommerce-gateway-sdm' ),
			'33' => __( 'Pick-up, expired card', 'woocommerce-gateway-sdm' ),
			'34' => __( 'Suspect Fraud', 'woocommerce-gateway-sdm' ),
			'35' => __( 'Pick-up, card acceptor contact acquirer', 'woocommerce-gateway-sdm' ),
			'36' => __( 'Pick up, card restricted', 'woocommerce-gateway-sdm' ),
			'37' => __( 'Pick up, call acquirer security', 'woocommerce-gateway-sdm' ),
			'38' => __( 'Pick up, Allowable PIN tries exceeded', 'woocommerce-gateway-sdm' ),
			'39' => __( 'No credit account', 'woocommerce-gateway-sdm' ),
			'40' => __( 'Requested function not supported', 'woocommerce-gateway-sdm' ),
			'41' => __( 'Pick up, lost card', 'woocommerce-gateway-sdm' ),
			'42' => __( 'No universal account', 'woocommerce-gateway-sdm' ),
			'43' => __( 'Pick up, stolen card', 'woocommerce-gateway-sdm' ),
			'44' => __( 'No investment account', 'woocommerce-gateway-sdm' ),
			'45' => __( 'Reserved for ISO use', 'woocommerce-gateway-sdm' ),
			'46' => __( 'Reserved for ISO use', 'woocommerce-gateway-sdm' ),
			'47' => __( 'Reserved for ISO use', 'woocommerce-gateway-sdm' ),
			'48' => __( 'Reserved for ISO use', 'woocommerce-gateway-sdm' ),
			'49' => __( 'Reserved for ISO use', 'woocommerce-gateway-sdm' ),
			'50' => __( 'Do not renew', 'woocommerce-gateway-sdm' ),
			'51' => __( 'Not sufficient funds', 'woocommerce-gateway-sdm' ),
			'52' => __( 'No chequing account', 'woocommerce-gateway-sdm' ),
			'53' => __( 'No savings account', 'woocommerce-gateway-sdm' ),
			'54' => __( 'Expired card / target', 'woocommerce-gateway-sdm' ),
			'55' => __( 'Incorrect PIN', 'woocommerce-gateway-sdm' ),
			'56' => __( 'No card record', 'woocommerce-gateway-sdm' ),
			'57' => __( 'Transaction not permitted to cardholder', 'woocommerce-gateway-sdm' ),
			'58' => __( 'Transaction not permitted to terminal', 'woocommerce-gateway-sdm' ),
			'59' => __( 'Suspected fraud', 'woocommerce-gateway-sdm' ),
			'60' => __( 'Card acceptor contact acquirer', 'woocommerce-gateway-sdm' ),
			'61' => __( 'Exceeds withdrawal amount limit', 'woocommerce-gateway-sdm' ),
			'62' => __( 'Restricted card', 'woocommerce-gateway-sdm' ),
			'63' => __( 'Security violation', 'woocommerce-gateway-sdm' ),
			'64' => __( 'Wrong original amount', 'woocommerce-gateway-sdm' ),
			'65' => __( 'Exceeds withdrawal frequency limit', 'woocommerce-gateway-sdm' ),
			'66' => __( 'Call acquirers security department', 'woocommerce-gateway-sdm' ),
			'67' => __( 'Card to be picked up at ATM', 'woocommerce-gateway-sdm' ),
			'68' => __( 'Response received too late', 'woocommerce-gateway-sdm' ),
			'69' => __( 'Reserved', 'woocommerce-gateway-sdm' ),
			'70' => __( 'Invalid transaction; contact card issuer', 'woocommerce-gateway-sdm' ),
			'71' => __( 'Decline PIN not changed', 'woocommerce-gateway-sdm' ),
			'72' => __( 'Reserved', 'woocommerce-gateway-sdm' ),
			'73' => __( 'Reserved', 'woocommerce-gateway-sdm' ),
			'74' => __( 'Reserved', 'woocommerce-gateway-sdm' ),
			'75' => __( 'Allowable number of PIN tries exceeded', 'woocommerce-gateway-sdm' ),
			'76' => __( 'Wrong PIN, number of PIN tries exceeded', 'woocommerce-gateway-sdm' ),
			'77' => __( 'Wrong Reference No.', 'woocommerce-gateway-sdm' ),
			'78' => __( 'Record Not Found', 'woocommerce-gateway-sdm' ),
			'79' => __( 'Already reversed', 'woocommerce-gateway-sdm' ),
			'80' => __( 'Network error', 'woocommerce-gateway-sdm' ),
			'81' => __( 'Foreign network error / PIN cryptographic error', 'woocommerce-gateway-sdm' ),
			'82' => __( 'Time-out at issuer system / Bad CVV (VISA)', 'woocommerce-gateway-sdm' ),
			'83' => __( 'Transaction failed', 'woocommerce-gateway-sdm' ),
			'84' => __( 'Pre-authorization timed out', 'woocommerce-gateway-sdm' ),
			'85' => __( 'No reason to decline', 'woocommerce-gateway-sdm' ),
			'86' => __( 'Unable to validate PIN', 'woocommerce-gateway-sdm' ),
			'87' => __( 'Purchase Approval Only', 'woocommerce-gateway-sdm' ),
			'88' => __( 'Cryptographic failure', 'woocommerce-gateway-sdm' ),
			'89' => __( 'Authentication failure', 'woocommerce-gateway-sdm' ),
			'90' => __( 'Cutoff is in progress', 'woocommerce-gateway-sdm' ),
			'91' => __( 'Issuer or switch is inoperative', 'woocommerce-gateway-sdm' ),
			'92' => __( 'Unable to route at acquirer module', 'woocommerce-gateway-sdm' ),
			'93' => __( 'Cannot be completed, violation of law', 'woocommerce-gateway-sdm' ),
			'94' => __( 'Duplicate Transmission', 'woocommerce-gateway-sdm' ),
			'95' => __( 'Reconcile error / Auth Not found', 'woocommerce-gateway-sdm' ),
			'96' => __( 'System Malfunction', 'woocommerce-gateway-sdm' ),
			'97' => __( 'Reserved', 'woocommerce-gateway-sdm' ),
			'98' => __( 'Reserved', 'woocommerce-gateway-sdm' ),
			'99' => __( 'Reserved', 'woocommerce-gateway-sdm' ),
		];
		if ( array_key_exists( $rc, $detailed_messages ) ) {
			return $detailed_messages[ $rc ];
		} else {
			return __( 'Unknown detailed response code', 'woocommerce-gateway-sdm' );
		}
	}

	/**
	 * Get additional message.
	 *
	 * @param string $auth_code Auth code.
	 *
	 * @return mixed|string|void
	 */
	public static function get_additional_message( $auth_code ) {
		$additional_messages = [
			'-1'  => __( 'Unidentified error', 'woocommerce-gateway-sdm' ),
			'-2'  => __( 'Required field in the requres is not filled out', 'woocommerce-gateway-sdm' ),
			'-3'  => __( 'Request did not pass CGI-check', 'woocommerce-gateway-sdm' ),
			'-4'  => __( 'Acquirer host (NS) does not respond or invalid format of template response file to e-Gateway module', 'woocommerce-gateway-sdm' ),
			'-5'  => __( 'No connection with acquirer host (NS)', 'woocommerce-gateway-sdm' ),
			'-6'  => __( 'Error of connection to acquirer host (NS) during transaction processing', 'woocommerce-gateway-sdm' ),
			'-7'  => __( 'Error in parameters of e-Gateway module', 'woocommerce-gateway-sdm' ),
			'-8'  => __( 'Invalid answer of acquirer host (NS), for example, no required fields', 'woocommerce-gateway-sdm' ),
			'-9'  => __( 'Error in the request "Card number" field', 'woocommerce-gateway-sdm' ),
			'-10' => __( 'Error in the request "Card expiration date" field', 'woocommerce-gateway-sdm' ),
			'-11' => __( 'Error in the request "Amount" field', 'woocommerce-gateway-sdm' ),
			'-12' => __( 'Error in the request "Currency" field', 'woocommerce-gateway-sdm' ),
			'-13' => __( 'Error in the request "Merchant ID" field', 'woocommerce-gateway-sdm' ),
			'-14' => __( 'IP-address of the transaction source (usually merchant) does not corresponds to the expected', 'woocommerce-gateway-sdm' ),
			'-15' => __( 'No connection with PIN-keyboard of Internet-terminal or agent program on Internet terminal computer is not running', 'woocommerce-gateway-sdm' ),
			'-16' => __( 'Error in the request "RRN" field', 'woocommerce-gateway-sdm' ),
			'-17' => __( 'Another transaction is running on the terminal', 'woocommerce-gateway-sdm' ),
			'-18' => __( 'Access from terminal to e-Gateway module is declined', 'woocommerce-gateway-sdm' ),
			'-19' => __( 'Error in the request "CVC2" or "CVC2 Description" field', 'woocommerce-gateway-sdm' ),
			'-20' => __( 'Error in the authentication request or authentication is unsuccessful', 'woocommerce-gateway-sdm' ),
			'-21' => __( 'Allowable time interval (1 hour by default) between value of "Time Stamp" field and time of e-Gateway module is exceeded', 'woocommerce-gateway-sdm' ),
			'-22' => __( 'Transaction has already done', 'woocommerce-gateway-sdm' ),
			'-23' => __( 'Error in transaction context', 'woocommerce-gateway-sdm' ),
			'-24' => __( 'Discrepancy in transaction context', 'woocommerce-gateway-sdm' ),
			'-25' => __( 'Transaction is terminated', 'woocommerce-gateway-sdm' ),
			'-26' => __( 'Wrong card BIN', 'woocommerce-gateway-sdm' ),
			'-27' => __( 'Error in merchant name', 'woocommerce-gateway-sdm' ),
			'-28' => __( 'Error in additional data', 'woocommerce-gateway-sdm' ),
			'-29' => __( 'Error in authentication link (corrupted or duplicated)', 'woocommerce-gateway-sdm' ),
		];
		if ( array_key_exists( $auth_code, $additional_messages ) ) {
			return $additional_messages[ $auth_code ];
		} else {
			return __( 'Unknown additional code', 'woocommerce-gateway-sdm' );
		}
	}
}
