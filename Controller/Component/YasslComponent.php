<?php
/**
 * YasslComponent
 *
 */
class YasslComponent extends Component
{
    public $force = false;
    public $detectors = array(
        array('env' => 'HTTPS', 'value' => true),
        array('env' => 'HTTPS', 'value' => 'on'),
    );

    public function initialize(Controller $controller)
    {
        $this->Controller = $controller;

        $this->Controller->request->addDetector('ssl', array(
            'callback' => array($this, 'addCallbackDetector'),
        ));

        if ($this->force === true && $this->Controller->request->is('ssl') !== true) {
            $this->forceSSL();
        }
    }

    /**
     * addCallbackDetector
     *
     */
    public function addCallbackDetector($request)
    {
        return $this->isSSL();
    }

    public function isSSL()
    {
        foreach ($this->detectors as $detector) {
            if (array_key_exists('value', $detector)) {
                if (env($detector['env']) === $detector['value']) {
                    return true;
                }
                continue;
            }
            if (array_key_exists('pattern', $detector)) {
                if (preg_match($detector['pattern'], env($detector['env']))) {
                    return true;
                }
                continue;
            }
        }

        return false;
    }

    public function forceSSL()
    {
        $url = 'https://' . env('SERVER_NAME') . $this->Controller->here;

        return $this->Controller->response->header('Location', Router::url($url, true));
    }

    public function forceNoSSL()
    {
        $url = 'http://' . env('SERVER_NAME') . $this->Controller->here;

        return $this->Controller->response->header('Location', Router::url($url, true));
    }

}
