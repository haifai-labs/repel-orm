<?php

namespace Repel\Adapter\Fetcher;

interface FetcherInterface {

    public function fetch(\PDO $pdo = null);

    public function setAdapter($adapter);
}
