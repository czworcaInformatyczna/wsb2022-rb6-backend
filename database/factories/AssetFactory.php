<?php

namespace Database\Factories;

use App\Enums\AssetStatus;
use App\Http\Controllers\AssetController;
use App\Models\AssetModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    public static $sampleImages = [
        // 150x150 PNG
        'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAACWBAMAAADOL2zRAAAAG1BMVEUAAP+WlpZdXb04ONcSEvElJeRLS8pwcLCDg6Ox7WOtAAAACXBIWXMAAA7EAAAOxAGVKw4bAAABAElEQVRoge3SMW+DMBiE4YsxJqMJtHOTITPeOsLQnaodGImEUMZEkZhRUqn92f0MaTubtfeMh/QGHANEREREREREREREtIJJ0xbH299kp8l8FaGtLdTQ19HjofxZlJ0m1+eBKZcikd9PWtXC5DoDotRO04B9YOvFIXmXLy2jEbiqE6Df7DTleA5socLqvEFVxtJyrpZFWz/pHM2CVte0lS8g2eDe6prOyqPglhzROL+Xye4tmT4WvRcQ2/m81p+/rdguOi8Hc5L/8Qk4vhZzy08DduGt9eVQyP2qoTM1zi0/uf4hvBWf5c77e69Gf798y08L7j0RERERERERERH9P99ZpSVRivB/rgAAAABJRU5ErkJggg==',
        // 300x300 PNG
        'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASwAAAEsBAMAAACLU5NGAAAAG1BMVEX/AACWlpajg4O9XV3xEhLKS0uwcHDXODjkJSUacfDXAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAD90lEQVR4nO3cwW+bSBQH4AcGw5HnJDhHaN3dHO1su9ojNGnPtrUb7dFuIiVHnEo5263Uv3vfGwab1myVA5DV6vcpgeD35HmeGYbJxUQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAD/zOb3N5BRexlu9/Jo+NCQFl/HbWrRK7s6Amcdy3jCfaftyOT/OmsnLxSFqkzu04Ns1Z+RxPOMtUc63fH6U5HP8O5/uo1Vyh9IJhTylwSjz0pV0y4Tex0dJ7iij3ck+WiV3J9RPvVhRLgO5O5V+KOSl7MesnXSRH++jNrlDAWurEW0i6ZOz8jI9mlwaDXkftckd8nXEdgnNVjI2sf6Q+VvLSMiMHJnupHC0j9rkrmlL87Lhs7JK86oM1fowVFq0jdrkjn2QKbMuTEvD8aGsfCQ9th9PbzHeR21yt1KWkUq3et+Tq4tDHpnXfZ67+7Zdltu1itrkbrEuRWVLWdmwHbl0shlXSQ7LLVtFbXLXZUmLphHOHK3IsWVtTg6Lk6PFV1Gb3G1Z9I1Xjb015NpSHq7jfntL7reoaW7JhD+pJQ2537llVuyGO1Em17iWJMt7f3ei/zeZcdGlKLDr1saW5XPV9F9bM2pV1CZ3yDxDZFx0HZcF0z+s8rpwVcuWPo5k1KqoTe7QwD58mp6Js/PUTn4tVEatx2ei3lAzu4M4t3uErQl5PN3YOb84NR+gitrkDnl8J51QNO23hjLH7SqQxxnp0trbfotmo9t0RE27U9k9hFw2PuBfLnVD0d/u9KMs8hNq2svrxFqXJXprZtmg9riXp5v0jTRI4afyn5lv1X8+gRaQ22XA/zT6sxatkgEAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAD65tjf/5gXLYtHb/8l8kNZkVw5zEwjIjei8ru7rtJ7/YqcO3ISorTFsiLvt+eXZY7xlp5sWd6b7KscrpeZ80DBus2y6D1dviY3C+QP/9WUnGWkp8GrhZa1fE3DQiK1ssYrurdlDeblwZ86TzTctFuWf/dxPihy+kw31+/IuTOnm2v98I6EwoTe1cuKLsLEluVm5cFLHHf7pc2JKIPoZl4STpfFHzSRfnEyc5pQrmVJiO7l13yRHpdlPQ0LW5ZTHSInWN23WZZMedMJycUq0aa1FT1F1dyK6MugoHpvuY903Fv0a9Jqb+n7apesHlY0KSvRU6233CV9V5Z/RsdzixbzlsvSuUXL4nFOT9mVtq2nw9yiYPx9WebCHGt3IrW7yOnby51IuyzPKEgv9M31dLgTKUgayioH+oqrdavlsp5hWPTb3jM9vnQBjZyLl64AAP43/gHVSaMe2vmdiAAAAABJRU5ErkJggg==',
        // 1200x1200 PNG
        'data:image/png;base64,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',
        // 150x150 JPG
        'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD//gA+Q1JFQVRPUjogZ2QtanBlZyB2MS4wICh1c2luZyBJSkcgSlBFRyB2NjIpLCBkZWZhdWx0IHF1YWxpdHkK/9sAQwAIBgYHBgUIBwcHCQkICgwUDQwLCwwZEhMPFB0aHx4dGhwcICQuJyAiLCMcHCg3KSwwMTQ0NB8nOT04MjwuMzQy/9sAQwEJCQkMCwwYDQ0YMiEcITIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIy/8AAEQgAlgCWAwEiAAIRAQMRAf/EAB8AAAEFAQEBAQEBAAAAAAAAAAABAgMEBQYHCAkKC//EALUQAAIBAwMCBAMFBQQEAAABfQECAwAEEQUSITFBBhNRYQcicRQygZGhCCNCscEVUtHwJDNicoIJChYXGBkaJSYnKCkqNDU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6g4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2drh4uPk5ebn6Onq8fLz9PX29/j5+v/EAB8BAAMBAQEBAQEBAQEAAAAAAAABAgMEBQYHCAkKC//EALURAAIBAgQEAwQHBQQEAAECdwABAgMRBAUhMQYSQVEHYXETIjKBCBRCkaGxwQkjM1LwFWJy0QoWJDThJfEXGBkaJicoKSo1Njc4OTpDREVGR0hJSlNUVVZXWFlaY2RlZmdoaWpzdHV2d3h5eoKDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uLj5OXm5+jp6vLz9PX29/j5+v/aAAwDAQACEQMRAD8A3KKKK+vPxsKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAoopG+6amcnGLaNqFONSrGEnZN2FyB3ozSHr2pOp/CsnVkm19x1RwtNwUrvbX52srfMdRSfw/hQvIzVqpeSj3VzGWGcabqX2dvmLRketJ/F+FJ05461Eqkk9uprDDU5K991p011069vmOzRTR/DTqulNzV3/AF1McVRVGfLF3/4Da/QKKKK0OYKKKKACiiigAooooAKKKKACjrRRQ1fRjTad0IQDQQDS0VLhF3utzVV6qtaT02129AxmjFFFPlV72I55cvLfQKTAzmlopOMXuhxq1Iq0ZNdN+nYTABpaKKailokKc5Td5O4UUUUyAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigD//Z'
    ];
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $image = rand(1,99) > 70 ? null : AssetController::parse_image(Arr::random(AssetFactory::$sampleImages));
        $userId = rand(1,99) > 70 ? null : User::inRandomOrder()->first()->id;
        $assetModelId = AssetModel::inRandomOrder()->first()->id;
        return [
            'name' => $this->faker->words(3, true),
            'tag' => $this->faker->bothify('?????-#####'),
            'asset_model_id' => $assetModelId,
            'image' => $image,
            'serial' => $this->faker->bothify('?????-#####'),
            'status' => AssetStatus::values()[array_rand(AssetStatus::values())],
            'current_holder_id' => $userId,
            'notes' => rand(1,99) > 50 ? null : $this->faker->text(1000),
            'warranty' => rand(1,99) > 50 ? null : $this->faker->numberBetween(1,24),
            'purchase_date' => rand(1,99) > 50 ? null : $this->faker->date(),
            'order_number' => rand(1,99) > 50 ? null : $this->faker->text(250),
            'price' => rand(1,99) > 50 ? null : floatval($this->faker->numberBetween(1, 10000)) * 1.5
        ];
    }

    public function with_image()
    {
        return $this->state(function (array $attributes) {
            if(!$attributes['image']) {
                return [
                    'image' => AssetController::parse_image(Arr::random(AssetFactory::$sampleImages))
                ];
            };
            return [];
        });
    }

    public function without_image()
    {
        return $this->state(function (array $attributes) {
            return [
                'image' => null
            ];
        });
    }
}
