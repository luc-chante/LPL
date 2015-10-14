<?php
/**
 * LPL
 *
 * PHP Version 5.4
 *
 * @copyright 2014 Luc Chante
 * @license CeCILL
 * @licence http://www.cecill.info/licences/Licence_CeCILL_V2.1-en.txt
 * 
 * @filesource
 */
namespace extensions {

    /**
     * trait Subject
     * 
     * Simple implementation of the observable pattern, using Standard PHP Library (SPL)
     *
     * ```php
     * class Subject extends SplSubject {
     *     use extensions\Subject;
     *
     *     public function SetData($data) {
     *         // Do something with the data
     *         $this->notify();
     *     }
     * }
     *
     * class Observer implements SplObserver {
     *     public function update(SplSubject $subject) {
     *         //...
     *     }
     * }
     * ```
     */
    trait Subject {
		/**
		 * internal observers storage
		 * @internal
		 */
        private $observers = [ ];
        
        // {{{ SplSubject implementstion
        /**
         * Attaches an SplObserver so that it can be notified of updates
         * 
         * @param \SplObserver $observer The observer to attach to
         */
        public function attach(\SplObserver $observer) {
            $key = spl_object_hash($observer);
            if (!isset($this->observers[$key])) {
                $this->observers[$key] = $observer;
            }
        }
        
        /**
         * Detaches an observer from the subject to no longer notify it of updates
         * 
         * @param \SplObserver $observer The observer to detach to
         */
        public function detach(\SplObserver $observer) {
            $key = spl_object_hash($observer);
            if (!isset($this->observers[$key])) {
                unset($this->observers[$key]);
            }
        }

        /**
         *  Notifies all attached observers
         */
        public function notify() {
            foreach ($this->observers as $observer) {
                $observer->update($this);
            }
        }
        // }}}
    }
}
