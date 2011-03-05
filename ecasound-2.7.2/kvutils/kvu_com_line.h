// -*- mode: C++; -*-
#ifndef INCLUDED_KVU_COM_LINE_H
#define INCLUDED_KVU_COM_LINE_H

#include <string>
#include <vector>

/**
 * Class representation of command line arguments
 */
class COMMAND_LINE {

private:
    
    std::vector<std::string> cparams;

    mutable std::vector<std::string>::size_type current_rep;

public:
   
    /**
     * Number of elements
     */
    std::string::size_type size() const { return(cparams.size()); }

    /**
     * Sets the first argument active. This is usually program's
     * name.
     */
    void begin(void) const { current_rep = 0; }

    /**
     * Moves to the next argument.
     */
    void next(void) const { ++current_rep; }

    /**
     * Moves to the previous argument.
     */
    void previous(void) const { --current_rep; }

    /** 
     * Returns true if we've past the last argument.
     */
    bool end(void) const { if (current_rep >= cparams.size()) return(true); else return (false); }
    
    /** 
     * Returns the current argument
     *
     * require:
     *  end() == false
     */
    const std::string& current(void) const { return(cparams[current_rep]); }

    /**
     * Is '-option' is among the arguments?
     *
     * ensure:
     *  current() == old current()
     */
    bool has(char option) const;

    /**
     * Is '-option' is among the arguments?
     */
    bool has(const std::string& option) const;

    /**
     * Make sure that all option tokens start with a '-' sign
     */
    void combine(void);

    /**
     * Static version of <code>combine</code>
     */
    static std::vector<std::string> combine(const std::vector<std::string>& source);

    /**
     * Adds 'argu' to the arguments.
     */
    void push_back(const std::string& argu);

    COMMAND_LINE(int argc, char *argv[]);
    COMMAND_LINE(const std::vector<std::string>& params);
    COMMAND_LINE(void);
};

#endif
