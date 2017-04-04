<?php

namespace Repel\Initiator;

use Repel\Includes\CLI;
use Repel\Framework;

class DatabaseInitiator extends RepelCli {

    private $db_config = null;

    public function __construct($db_config, $show_output = true) {
        $this->db_config = $db_config;

        parent::__construct($show_output);
    }

    public function init() {
        try {
            if ($this->db_config !== null) {
                if (!key_exists('databases', $this->db_config) || !Framework\RDatabaseConfigChecker::checkConfig($this->db_config["databases"])) {
                    $this->output(CLI::color("Incorrect database config file.", red));
                    return false;
                }
            } else {
                $this->output(CLI::color("No database config.", red));
                return false;
            }

            foreach ($this->db_config['databases'] as $key => $config) {
                $initiator = new Initiator($config);

                $this->output(CLI::h1('initialize database', self::$HEADER_FILL));
                $this->output(CLI::h2('add sources', self::$HEADER_FILL));

                if (key_exists("views_dir", $config)) {
                    $this->output(CLI::dotFill('loading views', self::$DOT_FILL));
                    $views = array_merge(glob($config['views_dir'] . "/**/*.sql"), glob($config['views_dir'] . "/*.sql"));

                    foreach ($views as $view) {
                        $initiator->addSource($view);
                    }

                    $this->output(CLI::color("done", green));
                    $this->output(CLI::newLine());
                }

                if (key_exists("functions_dir", $config)) {
                    $this->output(CLI::dotFill('loading functions', self::$DOT_FILL));
                    $functions = array_merge(glob($config['functions_dir'] . "/**/*.sql"), glob($config['functions_dir'] . "/*.sql"));

                    foreach ($functions as $function) {
                        $initiator->addSource($function);
                    }

                    $this->output(CLI::color("done", green));
                    $this->output(CLI::newLine());
                }

                if (key_exists("indexes_dir", $config)) {
                    $this->output(CLI::dotFill('loading indexes', self::$DOT_FILL));
                    $indexes = array_merge(glob($config['indexes_dir'] . "/**/*.sql"), glob($config['indexes_dir'] . "/*.sql"));

                    foreach ($indexes as $index) {
                        $initiator->addSource($index);
                    }

                    $this->output(CLI::color("done", green));
                    $this->output(CLI::newLine());
                }

                if (key_exists("triggers_dir", $config)) {
                    $this->output(CLI::dotFill('loading triggers', self::$DOT_FILL));
                    $triggers = array_merge(glob($config['triggers_dir'] . "/**/*.sql"), glob($config['triggers_dir'] . "/*.sql"));

                    foreach ($triggers as $trigger) {
                        $initiator->addSource($trigger);
                    }

                    $this->output(CLI::color("done", green));
                    $this->output(CLI::newLine());
                }

                if (key_exists("inserts_dir", $config)) {
                    $this->output(CLI::dotFill('loading inserts', self::$DOT_FILL));
                    if (is_array($config['inserts_dir'])) {
                        $inserts = array();

                        foreach ($config['inserts_dir'] as $insert) {
                            $inserts = array_merge($inserts, glob($insert . "/**/*.sql"), glob($insert . "/*.sql"));
                        }
                    } else {
                        $inserts = array_merge(glob($config['inserts_dir'] . "/**/*.sql"), glob($config['inserts_dir'] . "/*.sql"));
                    }

                    foreach ($inserts as $insert) {
                        $initiator->addSource($insert);
                    }

                    $this->output(CLI::color("done", green));
                    $this->output(CLI::newLine());
                }

                if (key_exists("sequences_dir", $config)) {
                    $this->output(CLI::dotFill('loading sequences', self::$DOT_FILL));

                    if (is_array($config['sequences_dir'])) {
                        $sequences = array();

                        foreach ($config['sequences_dir'] as $sequence) {
                            $sequences = array_merge($sequences, glob($sequence . "**/*.sql"), glob($sequence . "/*.sql"));
                        }
                    } else {
                        $sequences = array_merge(glob($config['sequences_dir'] . "**/*.sql"), glob($config['sequences_dir'] . "/*.sql"));
                    }


                    foreach ($sequences as $sequence) {
                        $initiator->addSource($sequence);
                    }

                    $this->output(CLI::color("done", green));
                    $this->output(CLI::newLine());
                }
                // @todo: modyfikatory
                $initiator->init();

                $this->output(CLI::success());
            }
        } catch (\Exception $ex) {
            $this->output(CLI::failure($ex));
            return false;
        }
    }

}
