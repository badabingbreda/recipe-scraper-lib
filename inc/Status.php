<?php
namespace RecipeRemix;

class Status {

    private $statuscode;
    private $settings;

    public function __construct( $statuscode , $settings = array() ) {

        $this->statuscode = $statuscode;
        $this->settings = $settings;

    }

    public function getStatus() {

        return array(
            'code' => $this->statuscode,
            'message' => $this->statusMessage( ),
        );
    }

    private function statusMessage() {

        $message = isset( $this->settings['message'] ) ? $this->settings['message'] : 'Some message';

        $message = apply_filters( "recipescraper/status/{$this->statuscode}" , $message );

        return $message;
    }


}