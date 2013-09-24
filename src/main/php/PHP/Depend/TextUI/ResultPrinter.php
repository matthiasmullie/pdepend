<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2013, Manuel Pichler <mapi@pdepend.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 */

namespace PHP\Depend\TextUI;

use PHP\Depend\Metrics\Analyzer;
use PHP\Depend\ProcessListener;
use PHP\Depend\Source\AST\AbstractASTArtifact;
use PHP\Depend\Source\Builder\Builder;
use PHP\Depend\Source\Tokenizer\Tokenizer;
use PHP\Depend\TreeVisitor\AbstractTreeVisitListener;

/**
 * Prints current the PDepend status information.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
class ResultPrinter extends AbstractTreeVisitListener implements ProcessListener
{
    /**
     * The step size.
     */
    const STEP_SIZE = 20;

    /**
     * Number of processed items.
     *
     * @var integer
     */
    private $count = 0;

    /**
     * Is called when PDepend starts the file parsing process.
     *
     * @param \PHP\Depend\Source\Builder\Builder $builder The used node builder instance.
     * @return void
     */
    public function startParseProcess(Builder $builder)
    {
        $this->count = 0;

        echo "Parsing source files:\n";
    }

    /**
     * Is called when PDepend has finished the file parsing process.
     *
     * @param \PHP\Depend\Source\Builder\Builder $builder The used node builder instance.
     * @return void
     */
    public function endParseProcess(Builder $builder)
    {
        $this->finish();
    }

    /**
     * Is called when PDepend starts parsing of a new file.
     *
     * @param \PHP\Depend\Source\Tokenizer\Tokenizer $tokenizer
     * @return void
     */
    public function startFileParsing(Tokenizer $tokenizer)
    {
        $this->step();
    }

    /**
     * Is called when PDepend has finished a file.
     *
     * @param \PHP\Depend\Source\Tokenizer\Tokenizer $tokenizer
     * @return void
     */
    public function endFileParsing(Tokenizer $tokenizer)
    {

    }

    /**
     * Is called when PDepend starts the analyzing process.
     *
     * @return void
     */
    public function startAnalyzeProcess()
    {
    }

    /**
     * Is called when PDepend has finished the analyzing process.
     *
     * @return void
     */
    public function endAnalyzeProcess()
    {
    }

    /**
     * Is called when PDepend starts the logging process.
     *
     * @return void
     */
    public function startLogProcess()
    {
        echo "Generating pdepend log files, this may take a moment.\n";
    }

    /**
     * Is called when PDepend has finished the logging process.
     *
     * @return void
     */
    public function endLogProcess()
    {
    }

    /**
     * Is called when PDepend starts a new analyzer.
     *
     * @param Analyzer $analyzer The context analyzer instance.
     * @return void
     */
    public function startAnalyzer(Analyzer $analyzer)
    {
        $this->count = 0;

        $name = substr(get_class($analyzer), 19, -9);
        echo "Executing {$name}-Analyzer:\n";
    }

    /**
     * Is called when PDepend has finished one analyzing process.
     *
     * @param \PHP\Depend\Metrics\Analyzer $analyzer The context analyzer instance.
     * @return void
     */
    public function endAnalyzer(Analyzer $analyzer)
    {
        $this->finish(self::STEP_SIZE);
    }

    /**
     * Generic notification method that is called for every node start.
     *
     * @param \PHP\Depend\Source\AST\AbstractASTArtifact $node
     * @return void
     */
    public function startVisitNode(AbstractASTArtifact $node)
    {
        $this->step(self::STEP_SIZE);
    }

    /**
     * Prints a single dot for the current step.
     *
     * @param integer $size The number of processed items that result in a new dot.
     * @return void
     */
    protected function step($size = 1)
    {
        if ($this->count > 0 && $this->count % $size === 0) {
            echo '.';
        }
        if ($this->count > 0 && $this->count % ($size * 60) === 0) {
            printf("% 6s\n", $this->count);
        }
        ++$this->count;
    }

    /**
     * Closes the current dot line.
     *
     * @param integer $size The number of processed items that result in a new dot.
     * @return void
     */
    protected function finish($size = 1)
    {
        $diff = ($this->count % ($size * 60));

        if ($diff === 0) {
            printf(".% 6s\n\n", $this->count);
        } else if ($size === 1) {
            $indent = 66 - ceil($diff / $size);
            printf(".% {$indent}s\n\n", $this->count);
        } else {
            $indent = 66 - ceil($diff / $size) + 1;
            printf("% {$indent}s\n\n", $this->count);
        }
    }
}
