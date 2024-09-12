<?php

namespace MapasCulturais\DoctrineMappings\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\Parser;

class RegexpReplace extends FunctionNode
{
    public $column = null;
    public $pattern = null;
    public $replacement = null;
    public $flags = null;

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->column = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);

        $this->pattern = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);

        $this->replacement = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);

        $this->flags = $parser->StringPrimary();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        return 'regexp_replace(' .
            $this->column->dispatch($sqlWalker) . ', ' .
            $this->pattern->dispatch($sqlWalker) . ', ' .
            $this->replacement->dispatch($sqlWalker) . ', ' .
            $this->flags->dispatch($sqlWalker) .
            ')';
    }
}