<?php

namespace spec\MyDrinks\Infrastructure\Application\Recipe;

use Aws\S3\S3Client;
use PhpSpec\ObjectBehavior;

final class S3UrlResolverSpec extends ObjectBehavior
{
    function it_generates_s3_image_path_using_prefix_and_bucket(S3Client $client)
    {
        $this->beConstructedWith($client, 'bucket', 'images');
        
        $client->getObjectUrl('bucket', 'images/image.jpg')->willReturn('https://s3-eu-west-1.amazonaws.com/bucket/images/image.jpg');
        
        $this->resolveUrlFor('image.jpg')->shouldReturn('https://s3-eu-west-1.amazonaws.com/bucket/images/image.jpg');
    }
}