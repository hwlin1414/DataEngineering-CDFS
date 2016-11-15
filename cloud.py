#!/usr/bin/env python
# -*- coding: utf-8 -*-

import errno
import os
import stat
import sys
import errno
import MySQLdb
import MySQLdb.cursors
import getpass
import datetime
import time

from fuse import FUSE, FuseOSError, Operations

debug = False
upload_path = '/data/DataEngineering/uploads'
host = 'localhost'
dbuser = 'cdfs'
dbpass = 'cdfstest'
dbname = 'cdfs'

def convtime(t):
    t = time.mktime(t.timetuple())
    return int(t)


class Passthrough(Operations):
    def __init__(self, user, pw, cursor):
        self.c = cursor
        self.c.execute("SELECT * FROM Users WHERE `name` = %s AND `password` = %s", (user, pw))
        result = self.c.fetchall()
        if len(result) != 1:
            print "Password Mismatch"
            exit(1)
        self.uid = result[0]['id']

    # Helpers
    # =======

    def _real_path(self, id):
        return "%s/%d" % (upload_path, id)

    def _get_id(self, paths):
        if debug: print "get_id(%s) entered" % (paths, )
        path = paths.split('/')
        if path[0] == '': del path[0]
        if path[-1] == '': del path[-1]
        dir_id = None
        if len(path) == 0:
            return ('DIR', None)

        self.c.execute("SELECT * FROM `Dirs` WHERE user_id = %s AND dir_id IS NULL AND name = %s", (self.uid, path[0]))
        result = self.c.fetchone()
        if result is not None:
            dir_id = result['id']
            del path[0]
            while len(path) > 0:
                self.c.execute("SELECT * FROM `Dirs` WHERE user_id = %s AND dir_id = %s AND name = %s", (self.uid, dir_id, path[0]))
                result = self.c.fetchone()
                if result is None:
                    break
                dir_id = result['id']
                del path[0]
            if len(path) == 0:
                return ('DIR', result['id'])
        if len(path) > 1:
            print "get id error:", paths, path
        elif dir_id is None:
            self.c.execute("SELECT * FROM `Files` WHERE user_id = %s AND dir_id IS NULL AND name = %s", (self.uid, path[0]))
            result = self.c.fetchone()
            if result is not None:
                return ('FILE', result['id'])
        else:
            self.c.execute("SELECT * FROM `Files` WHERE user_id = %s AND dir_id = %s AND name = %s", (self.uid, dir_id, path[0]))
            result = self.c.fetchone()
            if result is not None:
                return ('FILE', result['id'])
        return (None, None)

    # Filesystem methods
    # ==================

    def getattr(self, path, fh=None):
        if debug: print "getattr(%s) entered" % (path, )
        (t, i) = self._get_id(path)
        if t == 'DIR':
            if i is None:
                count = 2
                self.c.execute("SELECT * FROM `Dirs` WHERE user_id = %s AND dir_id IS NULL", (self.uid, ))
                count = count + self.c.rowcount
                self.c.execute("SELECT * FROM `Files` WHERE user_id = %s AND dir_id IS NULL", (self.uid, ))
                count = count + self.c.rowcount
                return {
                    'st_atime': 0,
                    'st_mtime': 0,
                    'st_ctime': 0,
                    'st_size': count,
                    'st_mode': stat.S_IRUSR | stat.S_IXUSR | stat.S_IFDIR,
                    'st_uid': os.getuid(),
                    'st_gid': os.getgid(),
                }
                
            count = 2
            self.c.execute("SELECT * FROM `Dirs` WHERE user_id = %s AND dir_id = %s", (self.uid, i))
            count = count + self.c.rowcount
            self.c.execute("SELECT * FROM `Files` WHERE user_id = %s AND dir_id = %s", (self.uid, i))
            count = count + self.c.rowcount

            self.c.execute("SELECT * FROM `Dirs` WHERE user_id = %s AND id = %s", (self.uid, i))
            result = self.c.fetchone()
            return {
                'st_atime': convtime(result['updated_at']),
                'st_mtime': convtime(result['updated_at']),
                'st_ctime': convtime(result['created_at']),
                'st_size': count,
                'st_mode': stat.S_IRUSR | stat.S_IXUSR | stat.S_IFDIR,
                'st_uid': os.getuid(),
                'st_gid': os.getgid(),
            }
        elif t == 'FILE':
            self.c.execute("SELECT * FROM `Files` WHERE user_id = %s AND id = %s", (self.uid, i))
            result = self.c.fetchone()
            s = os.stat(self._real_path(i))
            return {
                'st_atime': convtime(result['updated_at']),
                'st_mtime': convtime(result['updated_at']),
                'st_ctime': convtime(result['created_at']),
                'st_size': s.st_size,
                'st_mode': stat.S_IRUSR | stat.S_IXUSR | stat.S_IFREG,
                'st_uid': os.getuid(),
                'st_gid': os.getgid(),
            }
        raise FuseOSError(errno.ENOENT)

    def readdir(self, path, fh):
        if debug: print "readdir(%s) entered" % (path, )
        (t, i) = self._get_id(path)

        dirents = ['.', '..']
        if t == 'DIR':
            if i is not None:
                self.c.execute("SELECT name FROM `Dirs` WHERE user_id = %s AND dir_id = %s", (self.uid, i))
                result = self.c.fetchall()
                for n in result:
                    dirents.append(n['name'])
                self.c.execute("SELECT name FROM `Files` WHERE user_id = %s AND dir_id = %s", (self.uid, i))
                result = self.c.fetchall()
                for n in result:
                    dirents.append(n['name'])
            else:
                self.c.execute("SELECT name FROM `Dirs` WHERE user_id = %s AND dir_id IS NULL", (self.uid, ))
                result = self.c.fetchall()
                for n in result:
                    dirents.append(n['name'])
                self.c.execute("SELECT name FROM `Files` WHERE user_id = %s AND dir_id IS NULL", (self.uid, ))
                result = self.c.fetchall()
                for n in result:
                    dirents.append(n['name'])
        for r in dirents:
            yield r

    def rmdir(self, path):
        if debug: print "rmdir(%s) entered" % (path, )
        (t, i) = self._get_id(path)
        #if t == 'DIR':
        #    self.c.execute("DELETE FROM `Dirs` WHERE user_id = %s AND id = %s", (self.uid, i))
        raise FuseOSError(errno.ENOSYS)

    def mkdir(self, path, mode):
        if debug: print "mkdir(%s) entered" % (path, )
        raise FuseOSError(errno.ENOSYS)

    def unlink(self, path):
        if debug: print "unlink(%s) entered" % (path, )
        (t, i) = self._get_id(path)
        #if t == 'FILE':
        #    self.c.execute("DELETE FROM `Files` WHERE user_id = %s AND id = %s", (self.uid, i))
        raise FuseOSError(errno.ENOSYS)

    # File methods
    # ============

    def open(self, path, flags):
        if debug: print "open(%s) entered" % (path, )
        (t, i) = self._get_id(path)
        if t == 'FILE':
            return os.open(self._real_path(i), flags)
        raise FuseOSError(errno.ENOENT)

    def create(self, path, mode, fi=None):
        if debug: print "create(%s) entered" % (path, )
        #(t, i) = self._get_id(path)
        #if t == 'FILE':
        #    return os.open(self._real_path(i), os.O_WRONLY | os.O_CREAT, mode)
        raise FuseOSError(errno.ENOSYS)

    def read(self, path, length, offset, fh):
        if debug: print "read(%s) entered" % (path, )
        os.lseek(fh, offset, os.SEEK_SET)
        return os.read(fh, length)

    def write(self, path, buf, offset, fh):
        if debug: print "write(%s) entered" % (path, )
        os.lseek(fh, offset, os.SEEK_SET)
        return os.write(fh, buf)

    def release(self, path, fh):
        if debug: print "release(%s) entered" % (path, )
        return os.close(fh)

def main(user, mountpoint):
    db = MySQLdb.connect(host=host, user=dbuser, passwd=dbpass, db=dbname, cursorclass=MySQLdb.cursors.DictCursor, charset='utf8', init_command='SET NAMES UTF8')
    db.autocommit = True
    cursor = db.cursor()

    pw = getpass.getpass()
    FUSE(Passthrough(user, pw, cursor), mountpoint, nothreads=True, foreground=True, direct_io=True)

if __name__ == '__main__':
    if len(sys.argv) != 3:
        print "\tUsage: %s USER MOUNTPOINT" % (sys.argv[0])
        exit(2)
    main(sys.argv[1], sys.argv[2])
