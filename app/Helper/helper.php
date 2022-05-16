<?php
    function converStringToJson($response){
        foreach ($response as $key => $res) {
            info(json_encode($res));
            $award = json_decode($res->award);
            unset($res->award);
            $res->award = $award;
            info(json_encode($res));
        }
        return $response;
    }

?>
