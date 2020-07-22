<?php /** @noinspection PhpUnused */


    namespace ppm\Classes;

    use ArrayAccess;
    use ArrayIterator;
    use Countable;
    use IteratorAggregate;
    use ppm\Exceptions\JsonException;
    use ppm\Exceptions\RegexpException;
    use ppm\Interfaces\HtmlString;
    use Throwable;

    /**
     * Class Html
     * @package ppm\Classes
     */
    class Html implements ArrayAccess, Countable, IteratorAggregate, HtmlString
    {
        /** @var array<string, mixed>  element's attributes */
        public $attrs = [];

        /** @var bool  use XHTML syntax? */
        public static $xhtml = false;

        /** @var array<string, int>  void elements */
        public static $emptyElements = [
            'img' => 1, 'hr' => 1, 'br' => 1, 'input' => 1, 'meta' => 1, 'area' => 1, 'embed' => 1, 'keygen' => 1,
            'source' => 1, 'base' => 1, 'col' => 1, 'link' => 1, 'param' => 1, 'basefont' => 1, 'frame' => 1,
            'isindex' => 1, 'wbr' => 1, 'command' => 1, 'track' => 1,
        ];

        /** @var array<int, Html|string> nodes */
        protected $children = [];

        /** @var string  element's name */
        private $name;

        /** @var bool  is element empty? */
        private $isEmpty;

        /**
         * Constructs new HTML element.
         * @param string|null $name
         * @param array|string $attrs element's attributes or plain text content
         * @return static
         * @throws RegexpException
         */
        public static function el(string $name = null, $attrs = null)
        {
            $el = new static;
            $parts = explode(' ', (string) $name, 2);
            $el->setName($parts[0]);

            if (is_array($attrs))
            {
                $el->attrs = $attrs;

            }
            elseif ($attrs !== null)
            {
                $el->setText($attrs);
            }

            if (isset($parts[1]))
            {
                foreach (Strings::matchAll($parts[1] . ' ', '#([a-z0-9:-]+)(?:=(["\'])?(.*?)(?(2)\2|\s))?#i') as $m)
                {
                    $el->attrs[$m[1]] = $m[3] ?? true;
                }
            }

            return $el;
        }


        /**
         * Returns an object representing HTML text.
         * @param string $html
         * @return Html
         */
        public static function fromHtml(string $html): self
        {
            return (new static)->setHtml($html);
        }


        /**
         * Returns an object representing plain text.
         * @param string $text
         * @return Html
         */
        public static function fromText(string $text): self
        {
            return (new static)->setText($text);
        }


        /**
         * Converts to HTML.
         * @throws JsonException
         */
        final public function toHtml(): string
        {
            return $this->render();
        }


        /**
         * Converts to plain text.
         */
        final public function toText(): string
        {
            return $this->getText();
        }


        /**
         * Converts given HTML code to plain text.
         * @param string $html
         * @return string
         */
        public static function htmlToText(string $html): string
        {
            return html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }


        /**
         * Changes element's name.
         * @param string $name
         * @param bool|null $isEmpty
         * @return static
         */
        final public function setName(string $name, bool $isEmpty = null)
        {
            $this->name = $name;
            $this->isEmpty = $isEmpty ?? isset(static::$emptyElements[$name]);
            return $this;
        }


        /**
         * Returns element's name.
         */
        final public function getName(): string
        {
            return $this->name;
        }


        /**
         * Is element empty?
         */
        final public function isEmpty(): bool
        {
            return $this->isEmpty;
        }


        /**
         * Sets multiple attributes.
         * @param array $attrs
         * @return static
         */
        public function addAttributes(array $attrs)
        {
            $this->attrs = array_merge($this->attrs, $attrs);
            return $this;
        }


        /**
         * Appends value to element's attribute.
         * @param string $name
         * @param mixed $value
         * @param mixed $option
         * @return static
         */
        public function appendAttribute(string $name, $value, $option = true)
        {
            if (is_array($value))
            {
                $prev = isset($this->attrs[$name]) ? (array) $this->attrs[$name] : [];
                $this->attrs[$name] = $value + $prev;

            }
            elseif ((string) $value === '')
            {
                /** @noinspection PhpUnusedLocalVariableInspection */
                $tmp = &$this->attrs[$name]; // appending empty value? -> ignore, but ensure it exists
            }
            elseif (!isset($this->attrs[$name]) || is_array($this->attrs[$name]))
            { // needs array
                $this->attrs[$name][$value] = $option;

            }
            else
            {
                $this->attrs[$name] = [$this->attrs[$name] => true, $value => $option];
            }
            return $this;
        }


        /**
         * Sets element's attribute.
         * @param string $name
         * @param mixed $value
         * @return static
         */
        public function setAttribute(string $name, $value)
        {
            $this->attrs[$name] = $value;
            return $this;
        }


        /**
         * Returns element's attribute.
         * @param string $name
         * @return mixed
         */
        public function getAttribute(string $name)
        {
            return $this->attrs[$name] ?? null;
        }


        /**
         * Unsets element's attribute.
         * @param string $name
         * @return static
         */
        public function removeAttribute(string $name)
        {
            unset($this->attrs[$name]);
            return $this;
        }


        /**
         * Unsets element's attributes.
         * @param array $attributes
         * @return static
         */
        public function removeAttributes(array $attributes)
        {
            foreach ($attributes as $name)
            {
                unset($this->attrs[$name]);
            }
            return $this;
        }


        /**
         * Overloaded setter for element's attribute.
         * @param string $name
         * @param mixed $value
         */
        final public function __set(string $name, $value): void
        {
            $this->attrs[$name] = $value;
        }


        /**
         * Overloaded getter for element's attribute.
         * @param string $name
         * @return mixed
         */
        final public function &__get(string $name)
        {
            return $this->attrs[$name];
        }


        /**
         * Overloaded tester for element's attribute.
         * @param string $name
         * @return bool
         */
        final public function __isset(string $name): bool
        {
            return isset($this->attrs[$name]);
        }


        /**
         * Overloaded unsetter for element's attribute.
         * @param string $name
         */
        final public function __unset(string $name): void
        {
            unset($this->attrs[$name]);
        }


        /**
         * Overloaded setter for element's attribute.
         * @param string $m
         * @param array $args
         * @return mixed
         */
        final public function __call(string $m, array $args)
        {
            $p = substr($m, 0, 3);
            if ($p === 'get' || $p === 'set' || $p === 'add')
            {
                $m = substr($m, 3);
                $m[0] = $m[0] | "\x20";
                if ($p === 'get')
                {
                    return $this->attrs[$m] ?? null;

                }
                elseif ($p === 'add')
                {
                    $args[] = true;
                }
            }

            if (count($args) === 0)
            {
                return $this;
            }
            elseif (count($args) === 1)
            {
                $this->attrs[$m] = $args[0];
            }
            else
            {
                $this->appendAttribute($m, $args[0], $args[1]);
            }

            return $this;
        }


        /**
         * Special setter for element's attribute.
         * @param string $path
         * @param array|null $query
         * @return static
         */
        final public function href(string $path, array $query = null)
        {
            if ($query)
            {
                $query = http_build_query($query, '', '&');
                if ($query !== '')
                {
                    $path .= '?' . $query;
                }
            }
            $this->attrs['href'] = $path;
            return $this;
        }


        /**
         * Setter for data-* attributes. Booleans are converted to 'true' resp. 'false'.
         * @param string $name
         * @param mixed $value
         * @return static
         */
        public function data(string $name, $value = null)
        {
            if (func_num_args() === 1)
            {
                $this->attrs['data'] = $name;
            }
            else
            {
                $this->attrs["data-$name"] = is_bool($value) ? json_encode($value) : $value;
            }
            return $this;
        }


        /**
         * Sets element's HTML content.
         * @param  HtmlString|string  $html
         * @return static
         */
        final public function setHtml($html)
        {
            $this->children = [(string) $html];
            return $this;
        }


        /**
         * Returns element's HTML content.
         */
        final public function getHtml(): string
        {
            return implode('', $this->children);
        }


        /**
         * Sets element's textual content.
         * @param  HtmlString|string|int|float  $text
         * @return static
         */
        final public function setText($text)
        {
            if (!$text instanceof HtmlString)
            {
                $text = htmlspecialchars((string) $text, ENT_NOQUOTES, 'UTF-8');
            }
            $this->children = [(string) $text];
            return $this;
        }


        /**
         * Returns element's textual content.
         */
        final public function getText(): string
        {
            return self::htmlToText($this->getHtml());
        }


        /**
         * Adds new element's child.
         * @param  HtmlString|string  $child  Html node or raw HTML string
         * @return static
         */
        final public function addHtml($child)
        {
            return $this->insert(null, $child);
        }


        /**
         * Appends plain-text string to element content.
         * @param  HtmlString|string|int|float  $text
         * @return static
         */
        public function addText($text)
        {
            if (!$text instanceof HtmlString)
            {
                $text = htmlspecialchars((string) $text, ENT_NOQUOTES, 'UTF-8');
            }
            return $this->insert(null, $text);
        }


        /**
         * Creates and adds a new Html child.
         * @param string $name
         * @param array|string $attrs element's attributes or raw HTML string
         * @return static  created element
         * @throws RegexpException
         */
        final public function create(string $name, $attrs = null)
        {
            $this->insert(null, $child = static::el($name, $attrs));
            return $child;
        }


        /**
         * Inserts child node.
         * @param int|null $index
         * @param HtmlString|string $child Html node or raw HTML string
         * @param bool $replace
         * @return static
         */
        public function insert(?int $index, $child, bool $replace = false)
        {
            $child = $child instanceof self ? $child : (string) $child;
            if ($index === null)
            { // append
                $this->children[] = $child;

            }
            else
            { // insert or replace
                array_splice($this->children, $index, $replace ? 1 : 0, [$child]);
            }

            return $this;
        }


        /**
         * Inserts (replaces) child node (\ArrayAccess implementation).
         * @param  int|null  $index  position or null for appending
         * @param  Html|string  $child  Html node or raw HTML string
         */
        final public function offsetSet($index, $child): void
        {
            $this->insert($index, $child, true);
        }


        /**
         * Returns child node (\ArrayAccess implementation).
         * @param  int  $index
         * @return static|string
         */
        final public function offsetGet($index)
        {
            return $this->children[$index];
        }


        /**
         * Exists child node? (\ArrayAccess implementation).
         * @param int $index
         * @return bool
         * @return bool
         */
        final public function offsetExists($index): bool
        {
            return isset($this->children[$index]);
        }


        /**
         * Removes child node (\ArrayAccess implementation).
         * @param  int  $index
         */
        public function offsetUnset($index): void
        {
            if (isset($this->children[$index]))
            {
                array_splice($this->children, $index, 1);
            }
        }


        /**
         * Returns children count.
         */
        final public function count(): int
        {
            return count($this->children);
        }


        /**
         * Removes all children.
         */
        public function removeChildren(): void
        {
            $this->children = [];
        }


        /**
         * Iterates over elements.
         */
        final public function getIterator(): ArrayIterator
        {
            return new ArrayIterator($this->children);
        }


        /**
         * Returns all children.
         */
        final public function getChildren(): array
        {
            return $this->children;
        }


        /**
         * Renders element's start tag, content and end tag.
         * @param int|null $indent
         * @return string
         * @throws JsonException
         * @throws JsonException
         */
        final public function render(int $indent = null): string
        {
            $s = $this->startTag();

            if (!$this->isEmpty)
            {
                // add content
                if ($indent !== null)
                {
                    $indent++;
                }
                foreach ($this->children as $child)
                {
                    if ($child instanceof self)
                    {
                        $s .= $child->render($indent);
                    }
                    else
                    {
                        $s .= $child;
                    }
                }

                // add end tag
                $s .= $this->endTag();
            }

            if ($indent !== null)
            {
                return "\n" . str_repeat("\t", $indent - 1) . $s . "\n" . str_repeat("\t", max(0, $indent - 2));
            }
            return $s;
        }

        /**
         * @return string
         * @throws Throwable
         */
        final public function __toString(): string
        {
            try
            {
                return $this->render();
            }
            catch (Throwable $e)
            {
                //if (PHP_VERSION_ID >= 70400)
                //{
                //    throw $e;
                //}
                trigger_error('Exception in ' . __METHOD__ . "(): {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}", E_USER_ERROR);
            }
        }


        /**
         * Returns element's start tag.
         * @throws JsonException
         */
        final public function startTag(): string
        {
            return $this->name
                ? '<' . $this->name . $this->attributes() . (static::$xhtml && $this->isEmpty ? ' />' : '>')
                : '';
        }


        /**
         * Returns element's end tag.
         */
        final public function endTag(): string
        {
            return $this->name && !$this->isEmpty ? '</' . $this->name . '>' : '';
        }


        /**
         * Returns element's attributes.
         * @throws JsonException
         * @internal
         */
        final public function attributes(): string
        {
            if (!is_array($this->attrs))
            {
                return '';
            }

            $s = '';
            $attrs = $this->attrs;
            foreach ($attrs as $key => $value)
            {
                if ($value === null || $value === false)
                {
                    continue;

                }
                elseif ($value === true)
                {
                    if (static::$xhtml)
                    {
                        $s .= ' ' . $key . '="' . $key . '"';
                    }
                    else
                    {
                        $s .= ' ' . $key;
                    }
                    continue;

                }
                elseif (is_array($value))
                {
                    if (strncmp($key, 'data-', 5) === 0)
                    {
                        $value = Json::encode($value);
                    }
                    else
                    {
                        $tmp = null;
                        foreach ($value as $k => $v)
                        {
                            if ($v != null)
                            { // intentionally ==, skip nulls & empty string
                                // composite 'style' vs. 'others'
                                $tmp[] = $v === true ? $k : (is_string($k) ? $k . ':' . $v : $v);
                            }
                        }
                        if ($tmp === null)
                        {
                            continue;
                        }

                        $value = implode($key === 'style' || !strncmp($key, 'on', 2) ? ';' : ' ', $tmp);
                    }

                }
                elseif (is_float($value))
                {
                    $value = rtrim(rtrim(number_format($value, 10, '.', ''), '0'), '.');

                }
                else
                {
                    $value = (string) $value;
                }

                $q = strpos($value, '"') === false ? '"' : "'";
                $s .= ' ' . $key . '=' . $q
                    . str_replace(
                        ['&', $q, '<'],
                        ['&amp;', $q === '"' ? '&quot;' : '&#39;', self::$xhtml ? '&lt;' : '<'],
                        $value
                    )
                    . (strpos($value, '`') !== false && strpbrk($value, ' <>"\'') === false ? ' ' : '')
                    . $q;
            }

            $s = str_replace('@', '&#64;', $s);
            return $s;
        }


        /**
         * Clones all children too.
         */
        public function __clone()
        {
            foreach ($this->children as $key => $value)
            {
                if (is_object($value))
                {
                    $this->children[$key] = clone $value;
                }
            }
        }
    }