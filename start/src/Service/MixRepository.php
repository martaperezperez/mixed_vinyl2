<?php

namespace App\Service;

use Psr\Cache\CacheItemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Command\DebugCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class MixRepository
{
    public function __construct(
       private HttpClientInterface $githubContentClient ,
       private CacheInterface $cache,
        #[Autowire('%kernel.debug')]
        private bool $isDebug,
        #[Autowire(service: 'twig.command.debug')]
        private DebugCommand $twingDebugCommand
    ){
    }
public function findAll(): array{

    /*
        $output= new BufferedOutput();
        $this->twingDebugCommand->run(new ArrayInput([]), $output);
        dd($output); */

    return $this->cache->get('mixes_data', function(CacheItemInterface $cacheItem) {
        $cacheItem->expiresAfter($this->isDebug ? 5 : 60);
        $response = $this->githubContentClient->request('GET', '/SymfonyCasts/vinyl-mixes/main/mixes.json');
        return $response->toArray();
    });
}
}