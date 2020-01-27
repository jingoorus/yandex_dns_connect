class Swaper
{
    private $main_ip      = '188.225.17.106';

    private $emergency_ip = '127.0.0.1';

    private $token        = 'NR2U6Z4KBXWXWUG5353UWUTH2AML43HQGKFE2LZYGT7F3HPV7C3A';

    private $domain       = 'optic-city.ru';

    public  $current_ip   = false;

    public  $current_DNS  = NULL;

    public function __construct()
    {
        $this->current_DNS = dns_get_record( $this->domain , DNS_A );
    }

    public function isSiteAvailable()
    {
        $url = 'https://www.' . $this->domain;

        if( !filter_var( $url, FILTER_VALIDATE_URL ) ) {

          return false;
        }

        $curl = curl_init( $url );

        curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 10 );

        curl_setopt( $curl, CURLOPT_HEADER, true );

        curl_setopt( $curl, CURLOPT_NOBODY, true );

        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );

        $response = curl_exec( $curl );

        curl_close( $curl);

        return $response ? true : false;
    }

    public function getYandexData()
    {
        $curl = curl_init( 'https://pddimp.yandex.ru/api2/admin/dns/list?domain=' . $this->domain );

        curl_setopt( $curl, CURLOPT_HTTPHEADER, ['PddToken: ' . $this->token] );

        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );

        $response = curl_exec( $curl );

        curl_close( $curl );

        if ( $response ) {

            $json = json_decode( $response );
        }

        return $response ? $json : false;
    }

    public function editRecord($subdomain, $record_id, $new_ip, $ttl)
    {
        $curl = curl_init( 'https://pddimp.yandex.ru/api2/admin/dns/edit?domain=' . $this->domain . '&record_id=' . $record_id . '&subdomain=' . $subdomain . '&ttl=' . $ttl . '&content=' . $new_ip);

        curl_setopt( $curl, CURLOPT_HTTPHEADER, ['PddToken: ' . $this->token] );

        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );

        curl_setopt( $curl, CURLOPT_POST, 1 );

        $result = curl_exec( $curl );

        curl_close( $curl );    

        return $result;
    }
}
