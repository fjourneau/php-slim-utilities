<?php

namespace fjourneau\SlimUtilities;

use PHPUnit\Framework\TestCase;
use slim\Http\Response;

/**
 * Description of SlimTestCase
 * To be used with PHPUnit for UT.
  * ---------------------------------------
 * Notice thatit is designed for applications
 * built from  "Slim 3 FJO skeleton"
 *
 * @author fJourneau
 */
class SlimTestCase extends TestCase
{

    protected $app;

    public function setUp(): void
    {
        if (!$this->app) {
            $this->app = (new \boot\App())->get();
        }
    }

    public function getPage(string $url, string $url_params = ''): Response
    {
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI'    => $url,
            'QUERY_STRING' => $url_params
        ]);
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $this->app->getContainer()['request'] = $request;
        return $this->app->run(true);
    }

    public function postPage(string $url, string $url_params = '', array $params = []): Response
    {
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI'    => $url,
            'QUERY_STRING' => $url_params,
            'CONTENT_TYPE' => 'multipart/form-data',
        ]);
        /* Si paramètres POST à passer, on simule le passage */
        if ($params) {
            foreach ($params as $key => $value) {
                $_POST[$key] = $value;
            }
        }
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $this->app->getContainer()['request'] = $request;
        return $this->app->run(true);
    }

    public function readCsrfTokens(Response $response)
    {
        /* Récupération des tokens CSRF dans le HTML */
        $csrf_tokens = [];
        $csrf_tokens['name'] = "";
        $csrf_tokens['value'] = "";
        $body = (string)$response->getBody();
        if (preg_match('#<input type="hidden" name="csrf_value" value="(.*)">#isU', $body, $res)) {
            $csrf_tokens['value'] = $res[1];
        }
        if (preg_match('#<input type="hidden" name="csrf_name" value="(.*)">#isU', $body, $res)) {
            $csrf_tokens['name'] = $res[1];
        }
        return $csrf_tokens;
    }

    public function assertRedirectToLocation(Response $response, $location, $message = '')
    {
        // if($response->getStatusCode() <> 302){
        // }
        $this->assertSame($response->getStatusCode(), 302, $message);
        return $this->assertSame($response->getHeaderLine('Location'), $location, $message);
    }

    public function assertFlashContains(string $searched_string, $message = '')
    {
        $flashs = $_SESSION['slimFlash'] ?? [];

        if ($flashs === []) {
            $message = $this->_addMessageLine($message, "Messages flash vides, assurez vous d'appeler assertFlashContains() apres un postPage() ou avant getPage().");
            return $this->assertNotEmpty($flashs, $message);
        }

        $flash_txt = json_encode($flashs, JSON_UNESCAPED_UNICODE);
        $pos = strpos($flash_txt, $searched_string, 0);
        $this->assertGreaterThan(0, $pos, $message);

        // echo "\nPosition dans flash : " . $pos . "\n";
        // echo "\nFlash_txt : $flash_txt \n";
        // echo "\nsearched_string : $searched_string \n";
    }

    public function echoResponseText(Response $response)
    {
        $breaks_br = array("<br />", "<br>", "<br/>");
        $breaks_nl = array("\n", "\r");
        $body_clean_arr = [];

        $body = $response->getBody();
        $body = str_ireplace($breaks_nl, "\r\n", $body);
        $body = str_ireplace($breaks_br, "\r\n", $body);
        $body = strip_tags($body);
        $body_arr = explode("\r\n",$body);
        foreach($body_arr as $line){
            $l = trim($line);
            if($l <> ""){
                array_push($body_clean_arr, $l);
            }
        }
        echo join("\r\n", $body_clean_arr); 
    }

    public function testReturnTrue()
    {
        $this->assertTrue(true);
    }

    private function _addMessageLine(string $message, string $added_content)
    {
        if (trim($message)) {
            return $added_content . "\n" . $message;
        } else {
            return $added_content;
        }
    }
}