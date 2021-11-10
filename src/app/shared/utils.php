<?php

function processTemplate($templateName)
{
    ob_start();
    require "$templateName";
    $res = ob_get_contents();
    ob_end_clean();
    return $res;
}

function jsonHeader($urls = [], $code = '')
{
    $localLog = LogHelper::log()->withName('jsonHeader()');
    $localLog->addDebug('start', ['urls' => $urls, 'code' => $code, 'HTTP_ORIGIN' => isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'undefined']);

    $result = true;
    if ($code === 'accept') {
        header('Content-Type: application/json;charset=utf-8');
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Credentials: true');
    } else {
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'undefined';
        header('Content-Type: application/json;charset=utf-8');
        if (in_array($origin, $urls)) {
            header("Access-Control-Allow-Origin: $origin");
            header('Access-Control-Allow-Credentials: true');
        } else if (!empty($urls)){
            $result = false;
        }
    }
    return $result;
}

function fetchCurl($method, $url, $settings = [])
{
    $localLog = LogHelper::log()->withName('fetch()');
    $localLog->addDebug('start', ['method' => $method, 'url' => $url, 'settings' => $settings]);

    // defaults
    $settings['body'] = isset($settings['body']) ? $settings['body'] : '';

    $request = [];
    $request['method'] = $method;
    $request['body'] = $settings['body'];

    $ch = curl_init();

    if (isset($settings['query'])) {
        $url = $url . '?' . $settings['query'];
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    $request['url'] = $url;

    if ($method === 'post') {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $settings['body']);
    }

    if (isset($settings['ipResolve'])) {
        if ($settings['ipResolve'] === 'ipv4') {
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        }

        if ($settings['ipResolve'] === 'ipv6') {
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V6);
        }
    }

    if (isset($settings['headers'])) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $settings['headers']);
        $request['header'] = $settings['headers'];
    }

    if (isset($settings['userpwd'])) {
        curl_setopt($ch, CURLOPT_USERPWD, $settings['userpwd']);
    }

    if (isset($settings['verbose'])) {
        curl_setopt($ch, CURLOPT_VERBOSE, $settings['verbose']);
    }

    if (isset($settings['autoreferer'])) {
        curl_setopt($ch, CURLOPT_AUTOREFERER, $settings['autoreferer']);
    }

    if (isset($settings['referer'])) {
        curl_setopt($ch, CURLOPT_REFERER, $settings['referer']);
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $status = curl_getinfo($ch);

    curl_close($ch);

    $localLog->addDebug('finish', ['request' => $request, 'response' => $response, 'error' => $error, 'status' => $status]);
    return [$response, (empty($error) ? FALSE : $error), $status, $request];
}
