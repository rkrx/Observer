<?php
/**
 * Observer Library
 * Copyright (C) 2014 IceFlame.net
 *
 * Permission to use, copy, modify, and/or distribute this software for
 * any purpose with or without fee is hereby granted, provided that the
 * above copyright notice and this permission notice appear in all copies.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE
 * FOR ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY
 * DAMAGES WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER
 * IN AN ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING
 * OUT OF OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 *
 * @package  FlameCore\Observer
 * @version  1.1
 * @link     http://www.flamecore.org
 * @license  ISC License <http://opensource.org/licenses/ISC>
 */

namespace FlameCore\Observer\Responder;

/**
 * The Responder class
 *
 * @author   Christian Neff <christian.neff@gmail.com>
 */
class Responder implements ResponderInterface
{
    /**
     * The registered listeners
     *
     * @var array
     */
    protected $listeners = array();

    /**
     * Responds to an event
     *
     * @param string $event The name of the event ("action.event")
     * @param array $data The data for this event (optional)
     * @throws \InvalidArgumentException if the event name is invalid
     */
    public function respond($event, array $data = [])
    {
        $event = (string) $event;

        if (strpos($event, '.') == false)
            throw new \InvalidArgumentException('The event name is invalid (expecting "action.event")');

        foreach ($this->listeners as $pattern => $listener) {
            if (self::match($pattern, $event)) {
                call_user_func($listener, $data, $event);
            }
        }
    }

    /**
     * Gets the listener assigned to the given event
     *
     * @param string $event The name of the event
     * @return callable
     * @throws \InvalidArgumentException if the event name is invalid
     */
    public function getListener($event)
    {
        if (!is_string($event) || empty($event))
            throw new \InvalidArgumentException('The event name is invalid');

        return $this->listeners[$event];
    }

    /**
     * Assigns a listener to the given event
     *
     * @param string $event The event to assign the listener to
     * @param callable $listener The listener to register
     * @throws \InvalidArgumentException if the event name is invalid
     */
    public function setListener($event, callable $listener)
    {
        if (!is_string($event) || empty($event))
            throw new \InvalidArgumentException('The event name is invalid');

        $this->listeners[$event] = $listener;
    }

    /**
     * Checks if an event name matches the given pattern
     *
     * @param string $pattern The pattern to check the event name against
     * @param string $event The actual event name
     * @return bool Returns TRUE if the event name matches the pattern, FALSE otherwise
     */
    public static function match($pattern, $event)
    {
        if (strpos($pattern, '*') === false)
            return $event == $pattern;

        $event   = explode('.', $event);
        $pattern = explode('.', $pattern);

        if (count($event) != ($count = count($pattern)))
            return false;

        for ($i = 0; $i < $count; $i++) {
            if ($pattern[$i] != '*' && $pattern[$i] != $event[$i]) {
                return false;
            }
        }

        return true;
    }
}
