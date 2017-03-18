<?php

namespace Repel\Adapter\Fetcher;

interface FetcherInterface {

    public function fetch();
    public function setAdapter($adapter);
}
