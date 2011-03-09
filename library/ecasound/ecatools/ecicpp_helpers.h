#ifndef INCLUDED_ECICPP_HELPERS_H
#define INCLUDED_ECICPP_HELPERS_H

using std::string;

class ECA_CONTROL_INTERFACE;

int ecicpp_add_file_input(ECA_CONTROL_INTERFACE* eci, const string& filename, string* format);
int ecicpp_add_input(ECA_CONTROL_INTERFACE* eci, const string& input, string* format);
int ecicpp_add_output(ECA_CONTROL_INTERFACE* eci, const string& output, const string& format);
int ecicpp_connect_chainsetup(ECA_CONTROL_INTERFACE* eci, const string& csname);
int ecicpp_format_channels(const string& format);

#endif /* INCLUDED_ECICPP_HELPERS_H */
