#ifndef INCLUDED_AUDIOIO_MANAGER_H
#define INCLUDED_AUDIOIO_MANAGER_H

#include <string>
#include <list>

class AUDIO_IO;

/**
 * Virtual base class for implementing audio object managers.
 *
 * Key tasks of an object manager are to recognize 
 * AUDIO_IO objects that are of the type it manages, 
 * provide a communication platform for inter-object 
 * communication, and tracking of objects.
 *
 * Related design patterns:
 *     - Mediator (GoF273)
 *
 * @author Kai Vehmanen
 */
class AUDIO_IO_MANAGER : public DYNAMIC_OBJECT<std::string> {

 public:

  /** @name Public API */
  /*@{*/

  /**
   * Object name used to identify the object type. In most 
   * cases, object name is same for all class instances.
   * Must be implemented in all subclasses.
   */
  virtual std::string name(void) const = 0;

  /**
   * More verbose description of the manager type.
   */
  virtual std::string description(void) const = 0;

  /**
   * Whether 'aobj' is of the type handled by this 
   * manager object?
   *
   * @pre aobj != 0
   */
  virtual bool is_managed_type(const AUDIO_IO* aobj) const = 0;

  /**
   * Registers a new managed object. 
   *
   * Ownership of the object is not transfered to this 
   * manager object. It's therefore important to release 
   * all managed objects before they are allocated. 
   * Otherwise the manager object could end up referencing 
   * invalid memory regions.
   *
   * @pre aobj != 0
   * @post is_managed_type(aobj) == true
   */
  virtual void register_object(AUDIO_IO* aobj) = 0;

  /**
   * Gets an integer id of the registered object 'aobj'.
   *
   * @return -1 if not a registered object
   *
   * @pre is_managed_type(aobj) == true
   * @pre aobj != 0
   */
  virtual int get_object_id(const AUDIO_IO* aobj) const = 0;

  /**
   * Returns a list of all registered object ids.
   */
  virtual std::list<int> get_object_list(void) const = 0;

  /**
   * Unregisters object identified by 'id'.
   *
   * @post std::count(get_object_list().begin(), get_object_list().end(), id) == 0
   */
  virtual void unregister_object(int id) = 0;

  virtual ~AUDIO_IO_MANAGER(void) {}

  /*@}*/

};

#endif
