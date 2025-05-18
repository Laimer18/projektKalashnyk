<?php
class Image {
    private int $id;
    private string $image_url;
    private string $type;

    public function __construct(int $id, string $image_url, string $type) {
        $this->id = $id;
        $this->image_url = $image_url;
        $this->type = $type;
    }

    public function getId(): int { return $this->id; }
    public function getUrl(): string { return $this->image_url; }
    public function getType(): string { return $this->type; }
}
