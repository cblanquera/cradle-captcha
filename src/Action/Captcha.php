<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Framework\Captcha\Action;

use Cradle\Curl\CurlHandler;

use Cradle\Http\Request;
use Cradle\Http\Response;
use Cradle\Framework\App;

/**
 * Typical model create action steps
 *
 * @vendor   Cradle
 * @package  Framework
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Captcha
{
    const END_POINT = 'https://www.google.com/recaptcha/api/siteverify';
    /**
     * @const ATTACK Error template
     */
    const ATTACK = 'Captcha Failed';

    /**
     * @var string $no
     */
    public $no = 'captcha-406';

    /**
     * @var string $yes
     */
    public $yes = 'captcha-202';

    /**
     * @var App $app
     */
    protected $app = null;

    /**
     * Preps the Action binding the model given
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * check for valid
     *
     * @param *Request  $request
     * @param *Response $response
     *
     * @return Captcha
     */
    public function check(Request $request, Response $response) {
        $actual = $request->getStage('g-recaptcha-response');
        $config = $this->app->package('global')->service('captcha-main');

        $result = CurlHandler::i()
            ->setUrl(self::END_POINT)
            ->verifyHost(false)
            ->verifyPeer(false)
            ->setPostFields(http_build_query(array(
                'secret' => $config['secret'],
                'response' => $actual
            )))
            ->getJsonResponse();

        if(!isset($result['success']) || !$result['success']) {
            //prepare to error
            $message = $this->app->package('global')->translate(static::ATTACK);
            $response->setError(true, $message);

            //and trigger a subflow
            $this->app->subflow($this->no, $request, $response);
            return $this;
        }

        //it passed
        $this->app->subflow($this->yes, $request, $response);
        return $this;
    }

    /**
     * Loads a token to the request and response
     *
     * @param *Request  $request
     * @param *Response $response
     *
     * @return Captcha
     */
    public function load(Request $request, Response $response) {
        $config = $this->app->package('global')->service('captcha-main');

        //render the key
        $key = $config['token'];

        $response->setResults('captcha', $key);

        return $this;
    }

    /**
     * Renders a csrf field
     *
     * @param *Request  $request
     * @param *Response $response
     *
     * @return Csrf
     */
    public function render(Request $request, Response $response) {
        $key = $response->getResults('captcha');

        $content = $response->getContent();
        $content .= '<script src="https://www.google.com/recaptcha/api.js"></script>';
        $content .= '<div class="form-group"><div class="g-recaptcha" '
                    . 'data-sitekey="'.$key.'"></div></div>';
        $response->setContent($content);

        return $this;
    }
}
