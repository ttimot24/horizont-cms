<?php 

namespace App\Services;


class DashboardWidget {

    private string $name = "";
    private string $icon = "";
    private string $color = "primary";
    private int $value = 0;
    private string $link = "#";

    public function __construct(string $name, string $icon, int $value, string $link = "#", string $color = "primary") {
        $this->name = $name;
        $this->icon = $icon;
        $this->value = $value;
        $this->link = $link;
        $this->color = $color;
    }

    public static function builder(): self {
        return new self("", "", 0, "#", "primary");
    }

    public function build(): self {
        return $this;
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function setIcon(string $icon): self {
        $this->icon = $icon;
        return $this;
    }

    public function setValue(int $value): self {
        $this->value = $value;
        return $this;
    }

    public function setLink(string $link): self {
        $this->link = $link;
        return $this;
    }

    public function setColor(string $color): self {
        $this->color = $color;
        return $this;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getIcon(): string {
        return $this->icon;
    }

    public function getValue(): int {
        return $this->value;
    }

    public function getLink(): string {
        return $this->link;
    }

    public function getColor(): string {
        return $this->color;
    }

}