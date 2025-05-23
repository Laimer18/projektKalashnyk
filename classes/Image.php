<?php

class Image {
    private string $url;
    private string $type;

    public function __construct(string $url, string $type) {
        $this->url = $url;
        $this->type = $type;
    }

    public function getUrl(): string {
        return $this->url;
    }

    public function getType(): string {
        return $this->type;
    }
}
