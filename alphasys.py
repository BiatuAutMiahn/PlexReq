#!/usr/bin/env python3
import tornado.ioloop
import tornado.web
import tornado.websocket
import tornado.autoreload
import os
import sys
import json
from tornado.options import define, options, parse_command_line
import time
import hashlib
import base64
import random
import string
import logging
if sys.platform == 'linux':
    import systemd.daemon
import re
from datetime import datetime
import difflib
from watchdog.observers import Observer
from watchdog.events import PatternMatchingEventHandler
# from tmdbv3api import *
# from tmdbv3api import exceptions
import tmdbv3api

tmdb = tmdbv3api.TMDb()
tmdb.api_key = ''
tmdb.language = 'en'
tmdb_mov = tmdbv3api.Movie()
#
if sys.platform == 'linux':
    movies_path="/gDrive/Media/Video/Movies"
elif sys.platform == 'win32':
    movies_path="H:\\My Drive\\Media\\Video\\Movies"

movLocal={}
movReq={}
tStamps=[None,None,None,None]
movCache={'Meta':{},'Query':{}}
reloadLocal=False

def localReload():
    global movLocal
    global movCache
    global movReq
    global tStamps
    movLocal={}
    movReq={}
    tStamps=[None,None,None]
    movCache={'Meta':{},'Query':{}}

    if os.path.exists('PlexReq.json'):
        with open('PlexReq.json','r') as f:
            dat=f.read()
            dat=json.loads(dat)
            movReq=dat['Requests']
            movLocal=dat['Movies']
            movCache=dat['Cache']
            if 'tStamps' in dat:
                tStamps=dat['tStamps']
    for i,ts in enumerate(tStamps):
        if tStamps[i] is None:
            tStamps[i]=str(datetime.utcnow().isoformat()+'Z')
    for f in os.listdir(movies_path):
        if not os.path.isfile(os.path.join(movies_path, f)):
            continue
        f=re.sub('(.+), The \(','The \\1',f)
        s=re.search(r'(.*) \((\d{4})\)',f,re.M|re.I)
        if not s:
            continue
        # print(s.group(1)+" ("+s.group(2)+")")
        m=s.group(1)
        y=s.group(2)
        if not y in movLocal:
            movLocal[y]={}
        if not m in movLocal[y]:
            movLocal[y][m]=None
        if movLocal[y][m]!=None:
            for mreq in (movReq.copy()).keys():
                if mreq==str(movLocal[y][m]):
                    print("Removed "+m+"("+y+") from Queue")
                    movReq.pop(mreq)

            continue
        # print(movLocal[y][m])
        q=queryTMDB(m,y)
        if not len(q):
            print("NoRes:"+m+" ("+y+")")
            print(q)
            continue
        if len(q)>1:
            match=False
            for mid in q:
                if movCache['Meta'][mid]['title']==m:
                    movLocal[y][m]=mid
                    match=True
                    break
                else:
                    mndiff=difflib.SequenceMatcher(None,movCache['Meta'][mid]['title'],m).ratio()
                    if mndiff>0.9:
                        movLocal[y][m]=mid
                        match=True
                        break
                    else:
                        time.sleep(0.2)
                        print(m,y,[movCache['Meta'][mid]['title'],mndiff])

                # elif :
            if not match:
                print("MultiResMov:"+m+" ("+y+")")
                print(q)
                print('')
        else:
            movLocal[y][m]=q[0]
    for y in movLocal:
        for m in movLocal[y]:
            for mreq in (movReq.copy()).keys():
                if mreq==str(movLocal[y][m]):
                    print("Removed "+m+"("+y+") from Queue")
                    movReq.pop(mreq)
                    tStamps[2]=str(datetime.utcnow().isoformat()+'Z') # Meta
    with open('PlexReq.json','w+') as f:
        dat={}
        dat['Requests']=movReq
        dat['Movies']=movLocal
        dat['Cache']=movCache
        dat['tStamps']=tStamps
        f.write(json.dumps(dat,indent=2))

def queryTMDB(s,y=None,nc=None):
    q=None
    mo=[]
    missCache=False
    if nc==None:
        if y!=None:
            if y not in movCache['Query']:
                missCache=True
            else:
                if s not in movCache['Query'][y]:
                    missCache=True
        else:
            if s not in movCache['Query']:
                missCache=True
    if missCache:
        while True:
            try:
                q=tmdb_mov.search(s)
                break
            except Exception as e:
                if tmdb_mov._remaining < 1:
                    awai=abs(tmdb_mov._reset-int(time.time()))
                    time.sleep(awai)
                else:
                    return mo
        if not s:
            return false
        if y!=None:
            movCache['Query'][y]={}
            movCache['Query'][y][s]=[]
        else:
            movCache['Query'][s]=[]
        for i in q:
            ma={'title':None,'release_date':None,'poster_path':None,'overview':None}
            # print(dir(i))-
            # print(i.__dict__)-
            if 'release_date' in i._AsObj__entries:
                ma['release_date']=i.release_date
                if y!=None and str(y)!=ma['release_date'][0:4]:
                    continue
            if 'title' in i._AsObj__entries:
                ma['title']=i.title
            if 'poster_path' in i._AsObj__entries:
                ma['poster_path']=i.poster_path
            if 'overview' in i._AsObj__entries:
                ma['overview']=i.overview
            movCache['Meta'][i.id]=ma
            movCache['Query'][y][s].append(i.id)
            if y!=None:
                movCache['Query'][y][s].append(i.id)
            else:
                movCache['Query'][s].append(i.id)
            mo.append(i.id)
        tStamps[1]=str(datetime.utcnow().isoformat()+'Z') # Query
    else:
        if y!=None:
            q=movCache['Query'][y][s]
        else:
            q=movCache['Query'][s]
        for i in q:
            if not i in movCache['Meta']:
                continue
            if not movCache['Meta'][i]['release_date']:
                continue
            if y!=None and str(y)!=movCache['Meta'][i]['release_date'][0:4]:
                continue
            mo.append(q[i])
    return mo


class MetaModHandler(PatternMatchingEventHandler):
    global reloadLocal
    patterns = ["*.json"]
    def process(self, event):
        global reloadLocal
        """
        event.event_type
            'modified' | 'created' | 'moved' | 'deleted'
        event.is_directory
            True | False
        event.src_path
            path/to/observed/file
        """
        # the file will be processed there
        if "PlexReq.json" in event.src_path:
            reloadLocal=True
        # print event.src_path, event.event_type  # print now only for degug

    def on_modified(self, event):
        self.process(event)

class MovieModHandler(PatternMatchingEventHandler):
    global reloadLocal
    patterns = ["*.*"]
    def process(self, event):
        global reloadLocal
        """
        event.event_type
            'modified' | 'created' | 'moved' | 'deleted'
        event.is_directory
            True | False
        event.src_path
            path/to/observed/file
        """
        # the file will be processed there
        reloadLocal=True
        # print event.src_path, event.event_type  # print now only for degug

    def on_modified(self, event):
        self.process(event)

    def on_created(self, event):
        self.process(event)

# import code
# code.interact(local=dict(globals(), **locals()))

define("port", default=8818, type=int)
class IndexHandler(tornado.web.RequestHandler):
    def set_default_headers(self):
        self.set_header("Access-Control-Allow-Origin", "*")
        self.set_header("Access-Control-Allow-Headers", "x-requested-with")
        self.set_header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
    def get(self,req):
        global reloadLocal
        print('"'+req+'"')
        if req=='notify.json':
            self.write(json.dumps(tStamps))
        elif req=='requests.json':
            if reloadLocal:
                localReload()
                time.sleep(1)
                reloadLocal=False
            self.write(json.dumps(movReq))
        elif req=='movies.json':
            self.write(json.dumps(movLocal))
        elif req=='meta.json':
            self.write(json.dumps(movCache['Meta']))
        elif req=='query.json':
            self.write(json.dumps(movCache['Query']))
        elif req=='':
            with open('index.html','r') as html_s:
                self.write(html_s.read())
        else:
            with open(req,'rb') as html_s:
                self.write(html_s.read())
        # self.render("index.html")
    def post(self):
            j = self.get_argument("json")
            j=json.loads(j)
            print(json.dumps(j,indent=4))
            for y in movLocal:
                for m in movLocal[y]:
                    if str(j['id'])==str(movLocal[y][m]):
                        print({'result':'MovieExists'})
                        self.write(json.dumps({'result':'MovieExists'}))
                        return
            if str(j['id']) in movReq:
                print({'result':'QueueExists'})
                self.write(json.dumps({'result':'QueueExists'}))
                return
            ma={'title':j['title'],'release_date':j['release_date'],'poster_path':j['poster_path'],'overview':j['overview']}
            localReload();
            if str(j['id']) not in movCache['Meta']:
                movCache['Meta'][str(j['id'])]=ma
            if not str(j['id']) in movReq:
                movReq[str(j['id'])]="Waiting"
            tStamps[0]=str(datetime.utcnow().isoformat()+'Z')
            tStamps[1]=str(datetime.utcnow().isoformat()+'Z')
            with open('PlexReq.json','w+') as f:
                dat={}
                dat['Requests']=movReq
                dat['Movies']=movLocal
                dat['Cache']=movCache
                dat['tStamps']=tStamps
                f.write(json.dumps(dat,indent=2))
            print({'result':'Queued'})
            self.write(json.dumps({'result':'Queued'}))

# class RequestsHandler(tornado.web.RequestHandler):
#     def set_default_headers(self):
#         self.set_header("Access-Control-Allow-Origin", "*")
#         self.set_header("Access-Control-Allow-Headers", "x-requested-with")
#         self.set_header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
#     def get(self):
#         global reloadLocal
#         if reloadLocal:
#             localReload()
#             time.sleep(1)
#             reloadLocal=False
#         self.write(json.dumps(movReq))
# class MoviesHandler(tornado.web.RequestHandler):
#     global reloadLocal
#     def set_default_headers(self):
#         self.set_header("Access-Control-Allow-Origin", "*")
#         self.set_header("Access-Control-Allow-Headers", "x-requested-with")
#         self.set_header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
#     def get(self):
#         self.write(json.dumps(movLocal))
# class MetaHandler(tornado.web.RequestHandler):
#     global reloadLocal
#     def set_default_headers(self):
#         self.set_header("Access-Control-Allow-Origin", "*")
#         self.set_header("Access-Control-Allow-Headers", "x-requested-with")
#         self.set_header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
#     def get(self):
#         self.write(json.dumps(movCache['Meta']))
# class QueryHandler(tornado.web.RequestHandler):
#     global reloadLocal
#     def set_default_headers(self):
#         self.set_header("Access-Control-Allow-Origin", "*")
#         self.set_header("Access-Control-Allow-Headers", "x-requested-with")
#         self.set_header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
#     def get(self):
#         self.write(json.dumps(movCache['Query']))
# class NotifyHandler(tornado.web.RequestHandler):
#     global reloadLocal
#     def set_default_headers(self):
#         self.set_header("Access-Control-Allow-Origin", "*")
#         self.set_header("Access-Control-Allow-Headers", "x-requested-with")
#         self.set_header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
#     def get(self):
#         self.write(json.dumps(tStamps))

# class WebSocketHandler(tornado.websocket.WebSocketHandler):
#     def open(self, *args):
#         print(str(self.__dict__))
#     def on_message(self, message):
#         req=json.loads(message)
#         if not 'Init' in req:
#             ClientNotify(self,req['ClientId'],"ClientNotify",{"Error": "InvalidRequest"})
#             return
#         if req['Init']=="ServerNotify":
#             print(req)
#             return
#         if not 'ClientId' in req:
#             ClientNotify(self,None,"ClientNotify",{"Error": "InvalidRequest"})
#             return
#         print(req)
#         if 'SessionId' in req:
#             if not req['ClientId'] in Clients:
#                 ClientNotify(self,req['ClientId'],"ClientNotify",{"Error": "InvalidClient"})
#                 return
#             if req['Init']=="GetApp":
#                 ClientNotify(self,req['ClientId'],"InitApp",getAppPayload(req['Payload']))
#                 # ClientNotify(self,req['ClientId'],"ClientNotify",{"Error": "NotImplemented"})
#             elif req['Init']=="SyncTime":
#                 ClientNotify(self,req['ClientId'],"NotifyTime",{"Time":str(datetime.utcnow().isoformat()+'Z')})
#                 # ClientNotify(self,req['ClientId'],"ClientNotify",{"Error": "NotImplemented"})
#             elif req['Init']=="InitAppsRepo":
#                 ClientNotify(self,req['ClientId'],"UpdateAppsRepo",getAppRepo())
#                 # ClientNotify(self,req['ClientId'],"ClientNotify",{"Error": "NotImplemented"})
#             else:
#                 ClientNotify(self,req['ClientId'],"ClientNotify",{"Error": "UnknownRequest"})
#         else:
#             if req['Init']=="InitSession":
#                 if 'ClientId' in Clients and 'SessionId' in ret:
#                     if validateSession(req['ClientId'], req['SessionId']):
#                         ClientNotify(self,req['ClientId'],"SessionRestore",Sessions[Clients[req['ClientId']]['SessionPriv']]['SessionData'])
#                     else:
#                         ClientNotify(self,req['ClientId'],"ClientNotify",{"Error": "InvalidSession"})
#                 else:
#                     ClientNotify(self,req['ClientId'],"NewSession",{"SessonId":initSession(req['ClientId'])})
#             else:
#                 ClientNotify(self,req['ClientId'],"ClientNotify",{"Error": "InvalidRequest"})
#     def on_close(self):
#         print("Connection closed")
#
app = tornado.web.Application([
    (r'/(.*)', IndexHandler)#,
    # (r'/requests.json', RequestsHandler),
    # (r'/movies.json', MoviesHandler),
    # (r'/meta.json', MetaHandler),
    # (r'/query.json', QueryHandler),
    # (r'/notify.json', NotifyHandler),
    # (r'/(.*)', tornado.web.StaticFileHandler, {'path': "."})
])
#
#
if __name__ == '__main__':
    print('Infinity.PlexRequest v1.0')
    print('Starting up ...')
    localReload()
    print('Startup complete')
    # Tell systemd that our service is ready
    if sys.platform == 'linux':
        systemd.daemon.notify('READY=1')
    app.listen(options.port)
    logging.getLogger('tornado.access').disabled = True
    # tornado.autoreload.start()
    # for dir, _, files in os.walk('.'):
    #     [tornado.autoreload.watch(dir + '/' + f) for f in files if not f.startswith('.')]
    observer = Observer()
    observer.schedule(MetaModHandler(),'.')
    observer.schedule(MovieModHandler(),movies_path)
    observer.start()
    tornado.ioloop.IOLoop.instance().start()
