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
     * trait Observable
     * 
     * This trait mimic the SplSubject PHP interface and should be use with it.
     * 
     * exemple :
     * @source 2 Simple example for Singleton implementation
     * 
     *		class MyObservable implements SplSubject {
     *      	use extensions\Observable;
     *  	}
     *
     *  	class MyObserver implements SplObserver {
     *      	public function update(SplSubject $observable) {
     *          	if (func_num_args() == 1) {
     *              	echo "I've been updated";
     *          	}
     *          	else {
     *              	echo "I've been updated with " . strval(func_get_arg(1));
     *          	}
     *      	}
     *  	}
     *
     *  	$observer = new MyObserver();
     * 
     *  	$observable = new MyObservable();
     *  	$observable->attach($observer);
     *  	$observable->notify();
     * 		// result : I've been updated
     *  	$observable->notify("data");
     * 		// result : I've been updated with data
     */
    trait Observable {
		/**
		 * internal observers storage
		 * @internal
		 */
        private $observers = [];
        
        // {{{ SplSubject implementstion
        /**
         * Attaches an SplObserver so that it can be notified of updates.
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
         * Detaches an observer from the subject to no longer notify it of updates.
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
         *  Notifies all attached observers.
         * 
         * Unlike the standard definition, it is possible to pass a (single)
         * parameter to the observers.
         */
        public function notify() {
            if (func_num_args() == 1) {
                foreach ($this->observers as $observer) {
                    $observer->update($this, func_get_arg(0));
                }
            }
            else {
                foreach ($this->observers as $observer) {
                    $observer->update($this);
                }
            }
        }
        // }}}
    }
}
