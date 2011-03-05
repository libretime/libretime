#ifndef INCLUDED_KVU_FD_IO_H
#define INCLUDED_KVU_FD_IO_H

ssize_t kvu_fd_read(int fd, void *buf, size_t count, int timeout);
ssize_t kvu_fd_write(int fd, const void *buf, size_t count, int timeout);
int kvu_fd_wait(int fd, int timeout);

#endif
