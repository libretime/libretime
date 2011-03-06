#ifndef INCLUDED_TEXTDEBUG_H
#define INCLUDED_TEXTDEBUG_H

#include <string>
#include <iostream>

#include <eca-logger-interface.h>

class TEXTDEBUG : public ECA_LOGGER_INTERFACE {
   
 public:

    virtual void do_msg(ECA_LOGGER::Msg_level_t level, const std::string& module_name, const std::string& log_message);
    virtual void do_flush(void);
    virtual void do_log_level_changed(void) { }

    TEXTDEBUG(void);
    virtual ~TEXTDEBUG(void);

 private:

    std::ostream* dostream_repp;
    
    void stream(std::ostream* dos);
    std::ostream* stream(void);
};

#endif /* INCLUDED_TEXTDEBUG_H */
