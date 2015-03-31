(function(n) {
    function w(a, c) {
        function r() {}
        r.prototype = a;
        var b = new r,
            f;
        for (f in c) b[f] = c[f];
        c.toString !== Object.prototype.toString && (b.toString = c.toString);
        return b
    }

    function B(a) {
        return a instanceof Array ? function() {
            return m.iter(a)
        } : "function" == typeof a.iterator ? p(a, a.iterator) : a.iterator
    }

    function p(a, c) {
        if (null == c) return null;
        null == c.__id__ && (c.__id__ = G++);
        var b;
        null == a.hx__closures__ ? a.hx__closures__ = {} : b = a.hx__closures__[c.__id__];
        null == b && (b = function() {
            return b.method.apply(b.scope, arguments)
        },
            b.scope = a, b.method = c, a.hx__closures__[c.__id__] = b);
        return b
    }
    n.muses = n.muses || {};
    var s = function(a, c) {
        c = c.split("u").join("");
        this.r = new RegExp(a, c)
    };
    s.__name__ = !0;
    s.prototype = {
        r: null,
        match: function(a) {
            this.r.global && (this.r.lastIndex = 0);
            this.r.m = this.r.exec(a);
            this.r.s = a;
            return null != this.r.m
        },
        matched: function(a) {
            if (null != this.r.m && 0 <= a && a < this.r.m.length) return this.r.m[a];
            throw "EReg::matched";
        },
        __class__: s
    };
    var m = function() {};
    m.__name__ = !0;
    m.cca = function(a, c) {
        var b = a.charCodeAt(c);
        return b != b ? void 0 :
            b
    };
    m.substr = function(a, c, b) {
        if (null != c && 0 != c && null != b && 0 > b) return "";
        null == b && (b = a.length);
        0 > c ? (c = a.length + c, 0 > c && (c = 0)) : 0 > b && (b = a.length + b - c);
        return a.substr(c, b)
    };
    m.indexOf = function(a, c, b) {
        var e = a.length;
        0 > b && (b += e, 0 > b && (b = 0));
        for (; b < e;) {
            if (a[b] === c) return b;
            b++
        }
        return -1
    };
    m.remove = function(a, c) {
        var b = m.indexOf(a, c, 0);
        if (-1 == b) return !1;
        a.splice(b, 1);
        return !0
    };
    m.iter = function(a) {
        return {
            cur: 0,
            arr: a,
            hasNext: function() {
                return this.cur < this.arr.length
            },
            next: function() {
                return this.arr[this.cur++]
            }
        }
    };
    var x =
        function() {};
    x.__name__ = !0;
    x.exists = function(a, c) {
        for (var b = B(a)(); b.hasNext();) {
            var e = b.next();
            if (c(e)) return !0
        }
        return !1
    };
    var A = function() {
        this.length = 0
    };
    A.__name__ = !0;
    A.prototype = {
        h: null,
        length: null,
        iterator: function() {
            return {
                h: this.h,
                hasNext: function() {
                    return null != this.h
                },
                next: function() {
                    if (null == this.h) return null;
                    var a = this.h[0];
                    this.h = this.h[1];
                    return a
                }
            }
        },
        __class__: A
    };
    var g = n.MRP = function() {};
    g.__name__ = !0;
    g.setObject = function() {
        eval("MRP.instance = document." + g.objectId + ";");
        null == g.instance &&
        (g.instance = document.getElementById(g.objectId))
    };
    g.setElementId = function(a) {
        g.elementId = a
    };
    g.setObjectId = function(a) {
        g.objectId = a;
        g.setObject()
    };
    g.play = function() {
        g.instance.playSound()
    };
    g.stop = function() {
        g.instance.stopSound()
    };
    g.setVolume = function(a) {
        g.instance.setVolume(a / 100)
    };
    g.showInfo = function(a) {
        g.instance.showInfo(a)
    };
    g.setTitle = function(a) {
        g.instance.setTitle(a)
    };
    g.setUrl = function(a) {
        g.instance.setUrl(a)
    };
    g.setFallbackUrl = function(a) {
        g.instance.setFallbackUrl(a)
    };
    g.setCallbackFunction =
        function(a) {
            musesCallback = a
        };
    g.callbackExists = function() {
        var a = "error",
            a = typeof musesCallback;
        return "undefined" != a && "error" != a
    };
    g.getScriptBaseHREF = function() {
        return ("https:" == window.document.location.protocol ? "https://" : "http://") + "hosted.muses.org"
    };
    g.getSkin = function(a, c) {
        return -1 != a.indexOf("/") || c && ("original" == a || "tiny" == a) ? a : g.getScriptBaseHREF() + "/muses-" + a + ".xml"
    };
    g.insert = function(a) {
        null == a.elementId && null != g.elementId && (a.elementId = g.elementId);
        FlashDetect.versionAtLeast(10, 1) ? g.flashInsert(a) :
            g.jsInsert(a)
    };
    g.jsInsert = function(a) {
        a.autoplay = !1;
        g.playerCounter++;
        var c = "MusesRadioPlayer-HTML5-player-" + g.playerCounter,
            b = '<div id="' + c + '" style="width:' + a.width + "px;height:" + a.height + 'px"></div>';
        null == a.elementId ? window.document.write(b) : window.document.getElementById(a.elementId).innerHTML = b;
        a.elementId = c;
        a.skin = g.getSkin(a.skin, !1);
        new d.Muses(a)
    };
    g.flashInsert = function(a) {
        null == a.wmode && (a.wmode = "window");
        null == a.id && (a.id = g.objectId);
        var c = "url=" + a.url,
            c = c + ("&lang=" + (null != a.lang ? a.lang :
                    "auto")),
            c = c + ("&codec=" + a.codec),
            c = c + "&tracking=true" + ("&volume=" + (null != a.volume ? a.volume : 100));
        null != a.introurl && (c += "&introurl=" + a.introurl);
        null != a.autoplay && (c += "&autoplay=" + (a.autoplay ? "true" : "false"));
        null != a.jsevents && (c += "&jsevents=" + (a.jsevents ? "true" : "false"));
        null != a.buffering && (c += "&buffering=" + a.buffering);
        null != a.metadataProxy && (c += "&metadataproxy=" + a.metadataProxy);
        null != a.reconnectTime && (c += "&reconnecttime=" + a.reconnectTime);
        null != a.fallbackUrl && (c += "&fallback=" + a.fallbackUrl);
        var c =
                c + ("&skin=" + g.getSkin(a.skin, !0)),
            c = c + ("&title=" + a.title),
            c = c + ("&welcome=" + a.welcome),
            b = g.getScriptBaseHREF() + "/muses-hosted.swf",
            //b = "http://localhost/js/airtime/embeddableplayer/muses.swf",
            e = 'width="' + a.width + '" height="' + a.height + '" ';
        null != a.bgcolor && (e += 'bgcolor="' + a.bgcolor + '" ');
        var f = '<object id="' + a.id + '" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" ' + e + ">",
            f = f + ('<param name="movie" value="' + b + '" />') + ('<param name="flashvars" value="' + c + '" />'),
            f = f + ('<param name="wmode" value="' + a.wmode + '" />'),
            f = f + '<param name="allowScriptAccess" value="always" />',
            f = f + '<param name="scale" value="noscale" />';
        null != a.bgcolor && (f += '<param name="bgcolor" value="' + a.bgcolor + '" />');
        f += '<embed name="' + a.id + '" src="' + b + '" flashvars="' + c + '" scale="noscale" wmode="' + a.wmode + '" ' + e + ' allowScriptAccess="always" type="application/x-shockwave-flash" />';
        f += "</object>";
        null != a.callbackFunction ? g.setCallbackFunction(a.callbackFunction) : 1 != a.jsevents || g.callbackExists() || g.setCallbackFunction(function(a, c) {});
        null == a.elementId ? window.document.write(f) : window.document.getElementById(a.elementId).innerHTML =
            f;
        g.setObject()
    };
    g.main = function() {
        g.getScriptBaseHREF()
    };
    var z = function() {};
    z.__name__ = !0;
    z.prototype = {
        exists: null,
        remove: null,
        iterator: null,
        __class__: z
    };
    Math.__name__ = !0;
    var v = function() {};
    v.__name__ = !0;
    v.field = function(a, c) {
        try {
            return a[c]
        } catch (b) {
            return null
        }
    };
    v.setField = function(a, c, b) {
        a[c] = b
    };
    v.isFunction = function(a) {
        return "function" == typeof a && !(a.__name__ || a.__ename__)
    };
    var y = function() {};
    y.__name__ = !0;
    y.string = function(a) {
        return u.Boot.__string_rec(a, "")
    };
    y.parseInt = function(a) {
        var c = parseInt(a,
            10);
        0 != c || 120 != m.cca(a, 1) && 88 != m.cca(a, 1) || (c = parseInt(a));
        return isNaN(c) ? null : c
    };
    var C = function() {
        this.b = ""
    };
    C.__name__ = !0;
    C.prototype = {
        b: null,
        add: function(a) {
            this.b += y.string(a)
        },
        addSub: function(a, c, b) {
            this.b = null == b ? this.b + m.substr(a, c, null) : this.b + m.substr(a, c, b)
        },
        __class__: C
    };
    var t = function() {};
    t.__name__ = !0;
    t.urlEncode = function(a) {
        return encodeURIComponent(a)
    };
    t.isSpace = function(a, c) {
        var b = m.cca(a, c);
        return 8 < b && 14 > b || 32 == b
    };
    t.ltrim = function(a) {
        for (var c = a.length, b = 0; b < c && t.isSpace(a, b);) b++;
        return 0 < b ? m.substr(a, b, c - b) : a
    };
    t.rtrim = function(a) {
        for (var c = a.length, b = 0; b < c && t.isSpace(a, c - b - 1);) b++;
        return 0 < b ? m.substr(a, 0, c - b) : a
    };
    t.trim = function(a) {
        return t.ltrim(t.rtrim(a))
    };
    t.replace = function(a, c, b) {
        return a.split(c).join(b)
    };
    t.fastCodeAt = function(a, c) {
        return a.charCodeAt(c)
    };
    var D = function() {};
    D.__name__ = !0;
    D.getInstanceFields = function(a) {
        var c = [],
            b;
        for (b in a.prototype) c.push(b);
        m.remove(c, "__class__");
        m.remove(c, "__properties__");
        return c
    };
    var h = function() {};
    h.__name__ = !0;
    h.parse = function(a) {
        return q.xml.Parser.parse(a)
    };
    h.createElement = function(a) {
        var c = new h;
        c.nodeType = h.Element;
        c._children = [];
        c._attributes = new q.ds.StringMap;
        c.set_nodeName(a);
        return c
    };
    h.createPCData = function(a) {
        var c = new h;
        c.nodeType = h.PCData;
        c.set_nodeValue(a);
        return c
    };
    h.createCData = function(a) {
        var c = new h;
        c.nodeType = h.CData;
        c.set_nodeValue(a);
        return c
    };
    h.createComment = function(a) {
        var c = new h;
        c.nodeType = h.Comment;
        c.set_nodeValue(a);
        return c
    };
    h.createDocType = function(a) {
        var c = new h;
        c.nodeType = h.DocType;
        c.set_nodeValue(a);
        return c
    };
    h.createProcessingInstruction =
        function(a) {
            var c = new h;
            c.nodeType = h.ProcessingInstruction;
            c.set_nodeValue(a);
            return c
        };
    h.createDocument = function() {
        var a = new h;
        a.nodeType = h.Document;
        a._children = [];
        return a
    };
    h.prototype = {
        nodeType: null,
        _nodeName: null,
        _nodeValue: null,
        _attributes: null,
        _children: null,
        _parent: null,
        get_nodeName: function() {
            if (this.nodeType != h.Element) throw "bad nodeType";
            return this._nodeName
        },
        set_nodeName: function(a) {
            if (this.nodeType != h.Element) throw "bad nodeType";
            return this._nodeName = a
        },
        set_nodeValue: function(a) {
            if (this.nodeType ==
                h.Element || this.nodeType == h.Document) throw "bad nodeType";
            return this._nodeValue = a
        },
        get: function(a) {
            if (this.nodeType != h.Element) throw "bad nodeType";
            return this._attributes.get(a)
        },
        set: function(a, c) {
            if (this.nodeType != h.Element) throw "bad nodeType";
            this._attributes.set(a, c)
        },
        exists: function(a) {
            if (this.nodeType != h.Element) throw "bad nodeType";
            return this._attributes.exists(a)
        },
        attributes: function() {
            if (this.nodeType != h.Element) throw "bad nodeType";
            return this._attributes.keys()
        },
        elements: function() {
            if (null ==
                this._children) throw "bad nodetype";
            return {
                cur: 0,
                x: this._children,
                hasNext: function() {
                    for (var a = this.cur, c = this.x.length; a < c && this.x[a].nodeType != h.Element;) a += 1;
                    this.cur = a;
                    return a < c
                },
                next: function() {
                    for (var a = this.cur, c = this.x.length; a < c;) {
                        var b = this.x[a],
                            a = a + 1;
                        if (b.nodeType == h.Element) return this.cur = a, b
                    }
                    return null
                }
            }
        },
        addChild: function(a) {
            if (null == this._children) throw "bad nodetype";
            null != a._parent && m.remove(a._parent._children, a);
            a._parent = this;
            this._children.push(a)
        },
        __class__: h
    };
    var b = {
        Campaign: function(a) {
            this.responseCount =
                0;
            "direct" != a && "organic" != a && "referral" != a && b.Tracker._raiseError("Campaign type has to be one of the Campaign::TYPE_* constant values.", "Campaign.new");
            this.type = a;
            switch (a) {
                case "direct":
                    this.source = this.name = "(direct)";
                    this.medium = "(none)";
                    break;
                case "referral":
                    this.name = "(referral)";
                    this.medium = "referral";
                    break;
                case "organic":
                    this.name = "(organic)", this.medium = "organic"
            }
            this.creationTime = new b.DateTime
        }
    };
    b.Campaign.__name__ = !0;
    b.Campaign.createFromReferrer = function(a) {
        var c = new b.Campaign("referral");
        a = new b.URLParser(a);
        c.source = a.host;
        c.content = a.path;
        return c
    };
    b.Campaign.prototype = {
        type: null,
        creationTime: null,
        responseCount: null,
        id: null,
        source: null,
        gClickId: null,
        dClickId: null,
        name: null,
        medium: null,
        term: null,
        content: null,
        validate: function() {
            null == this.source && b.Tracker._raiseError('Campaigns need to have at least the "source" attribute defined.', "Campaign.validate")
        },
        setType: function(a) {
            this.type = a
        },
        getType: function() {
            return this.type
        },
        setCreationTime: function(a) {
            this.creationTime = a
        },
        getCreationTime: function() {
            return this.creationTime
        },
        setResponseCount: function(a) {
            this.responseCount = a
        },
        getResponseCount: function() {
            return this.responseCount
        },
        increaseResponseCount: function(a) {
            null == a && (a = 1);
            this.responseCount += a
        },
        setId: function(a) {
            this.id = a
        },
        getId: function() {
            return this.id
        },
        setSource: function(a) {
            this.source = a
        },
        getSource: function() {
            return this.source
        },
        setGClickId: function(a) {
            this.gClickId = a
        },
        getGClickId: function() {
            return this.gClickId
        },
        setDClickId: function(a) {
            this.dClickId = a
        },
        getDClickId: function() {
            return this.dClickId
        },
        setName: function(a) {
            this.name =
                a
        },
        getName: function() {
            return this.name
        },
        setMedium: function(a) {
            this.medium = a
        },
        getMedium: function() {
            return this.medium
        },
        setTerm: function(a) {
            this.term = a
        },
        getTerm: function() {
            return this.term
        },
        setContent: function(a) {
            this.content = a
        },
        getContent: function() {
            return this.content
        },
        __class__: b.Campaign
    };
    b.Config = function(a) {
        null == a && (a = !1);
        this.sitespeedSampleRate = 1;
        this.endPointPath = "/__utm.gif";
        this.endPointHost = "www.google-analytics.com";
        this.urlScheme = "http";
        this.requestTimeout = 1;
        this.sendOnShutdown = this.fireAndForget = !1;
        this.errorSeverity = 2;
        this.setUrlScheme("http" + (a ? "s" : ""))
    };
    b.Config.__name__ = !0;
    b.Config.prototype = {
        errorSeverity: null,
        sendOnShutdown: null,
        fireAndForget: null,
        loggingCallback: null,
        requestTimeout: null,
        urlScheme: null,
        endPointHost: null,
        endPointPath: null,
        sitespeedSampleRate: null,
        getErrorSeverity: function() {
            return this.errorSeverity
        },
        setErrorSeverity: function(a) {
            this.errorSeverity = a
        },
        getSendOnShutdown: function() {
            return this.sendOnShutdown
        },
        setSendOnShutdown: function(a) {
            this.sendOnShutdown = a
        },
        getFireAndForget: function() {
            return this.fireAndForget
        },
        setFireAndForget: function(a) {
            this.fireAndForget = a
        },
        getLoggingCallback: function() {
            return this.loggingCallback
        },
        setLoggingCallback: function(a) {
            this.loggingCallback = a
        },
        getRequestTimeout: function() {
            return this.requestTimeout
        },
        setRequestTimeout: function(a) {
            this.requestTimeout = a
        },
        getUrlScheme: function() {
            return this.urlScheme
        },
        setUrlScheme: function(a) {
            return this.urlScheme = a
        },
        getEndPointHost: function() {
            return this.endPointHost
        },
        setEndPointHost: function(a) {
            this.endPointHost = a
        },
        getEndPointPath: function() {
            return this.endPointPath
        },
        setEndPointPath: function(a) {
            this.endPointPath = a
        },
        getSitespeedSampleRate: function() {
            return this.sitespeedSampleRate
        },
        setSitespeedSampleRate: function(a) {
            0 > a || 100 < a ? b.Tracker._raiseError("For consistency with ga.js, sample rates must be specified as a number between 0 and 100.", "config.setSitespeedSampleRate") : this.sitespeedSampleRate = a
        },
        __class__: b.Config
    };
    b.CustomVariable = function(a, c, b, e) {
        null == e && (e = 0);
        null == a && (a = 0);
        this.scope = 3;
        0 != a && this.setIndex(a);
        null != c && this.setName(c);
        null != b && this.setValue(b);
        0 != e && this.setScope(e)
    };
    b.CustomVariable.__name__ = !0;
    b.CustomVariable.prototype = {
        index: null,
        name: null,
        value: null,
        scope: null,
        validate: function() {
            128 < (this.name + y.string(this.value)).length && b.Tracker._raiseError("Custom Variable combined name and value length must not be larger than 128 bytes.", "CustomVariable.validate")
        },
        getIndex: function() {
            return this.index
        },
        setIndex: function(a) {
            (1 > a || 5 < a) && b.Tracker._raiseError("Custom Variable index has to be between 1 and 5.", "CustomVariable.setIndex");
            this.index =
                a
        },
        getName: function() {
            return this.name
        },
        setName: function(a) {
            this.name = a
        },
        getValue: function() {
            return this.value
        },
        setValue: function(a) {
            this.value = a
        },
        getScope: function() {
            return this.scope
        },
        setScope: function(a) {
            3 != a && 2 != a && 1 != a && b.Tracker._raiseError("Custom Variable scope has to be one of the CustomVariable::SCOPE_* constant values.", "CustomVariable.setScope");
            this.scope = a
        },
        __class__: b.CustomVariable
    };
    b.DateTime = function(a) {
        this.date = null == a ? Math.round((new Date).getTime()) + "" : a
    };
    b.DateTime.__name__ = !0;
    b.DateTime.prototype = {
        date: null,
        toString: function() {
            return this.date
        },
        __class__: b.DateTime
    };
    b.Event = function(a, c, b, e, f) {
        null == f && (f = !1);
        null == e && (e = 0);
        this.noninteraction = !1;
        null != a && this.setCategory(a);
        null != c && this.setAction(c);
        null != b && this.setLabel(b);
        this.setValue(e);
        this.setNoninteraction(f)
    };
    b.Event.__name__ = !0;
    b.Event.prototype = {
        category: null,
        action: null,
        label: null,
        value: null,
        noninteraction: null,
        validate: function() {
            null != this.category && null != this.action || b.Tracker._raiseError("Events need at least to have a category and action defined.",
                "Event.validate")
        },
        getCategory: function() {
            return this.category
        },
        setCategory: function(a) {
            this.category = a
        },
        getAction: function() {
            return this.action
        },
        setAction: function(a) {
            this.action = a
        },
        getLabel: function() {
            return this.label
        },
        setLabel: function(a) {
            this.label = a
        },
        getValue: function() {
            return this.value
        },
        setValue: function(a) {
            this.value = a
        },
        getNoninteraction: function() {
            return this.noninteraction
        },
        setNoninteraction: function(a) {
            this.noninteraction = a
        },
        __class__: b.Event
    };
    b.Item = function() {
        this.quantity = 1
    };
    b.Item.__name__ = !0;
    b.Item.prototype = {
        orderId: null,
        sku: null,
        name: null,
        variation: null,
        price: null,
        quantity: null,
        validate: function() {
            null == this.sku && b.Tracker._raiseError("Items need to have a sku/product code defined.", "Item.validate")
        },
        getOrderId: function() {
            return this.orderId
        },
        setOrderId: function(a) {
            this.orderId = a
        },
        getSku: function() {
            return this.sku
        },
        setSku: function(a) {
            this.sku = a
        },
        getName: function() {
            return this.name
        },
        setName: function(a) {
            this.name = a
        },
        getVariation: function() {
            return this.variation
        },
        setVariation: function(a) {
            this.variation =
                a
        },
        getPrice: function() {
            return this.price
        },
        setPrice: function(a) {
            this.price = a
        },
        getQuantity: function() {
            return this.quantity
        },
        setQuantity: function(a) {
            this.quantity = a
        },
        __class__: b.Item
    };
    b.Page = function(a) {
        this.setPath(a)
    };
    b.Page.__name__ = !0;
    b.Page.prototype = {
        path: null,
        title: null,
        charset: null,
        referrer: null,
        loadTime: null,
        setPath: function(a) {
            null != a && "/" != a.charAt(0) && b.Tracker._raiseError('The page path should always start with a slash ("/").', "Page.setPath");
            this.path = a
        },
        getPath: function() {
            return this.path
        },
        setTitle: function(a) {
            this.title = a
        },
        getTitle: function() {
            return this.title
        },
        setCharset: function(a) {
            this.charset = a
        },
        getCharset: function() {
            return this.charset
        },
        setReferrer: function(a) {
            this.referrer = a
        },
        getReferrer: function() {
            return this.referrer
        },
        setLoadTime: function(a) {
            this.loadTime = a
        },
        getLoadTime: function() {
            return this.loadTime
        },
        __class__: b.Page
    };
    b.Session = function() {
        this.setSessionId(this.generateSessionId());
        this.setTrackCount(0);
        this.setStartTime(new b.DateTime)
    };
    b.Session.__name__ = !0;
    b.Session.prototype = {
        sessionId: null,
        trackCount: null,
        startTime: null,
        fromUtmb: function(a) {
            a = a.split(".");
            if (4 != a.length) return b.Tracker._raiseError('The given "__utmb" cookie value is invalid.', "Session.fromUtmb"), this;
            this.setTrackCount(b.internals.Util.parseInt(a[1], 0));
            this.setStartTime(new b.DateTime(a[3]));
            return this
        },
        generateSessionId: function() {
            return b.internals.Util.generate32bitRandom()
        },
        getSessionId: function() {
            return this.sessionId
        },
        setSessionId: function(a) {
            this.sessionId = a
        },
        getTrackCount: function() {
            return this.trackCount
        },
        setTrackCount: function(a) {
            this.trackCount = a
        },
        increaseTrackCount: function(a) {
            null == a && (a = 1);
            this.trackCount += a
        },
        getStartTime: function() {
            return this.startTime
        },
        setStartTime: function(a) {
            this.startTime = a
        },
        __class__: b.Session
    };
    b.SocialInteraction = function(a, c, b) {
        null != a && this.setNetwork(a);
        null != c && this.setAction(c);
        null != b && this.setTarget(b)
    };
    b.SocialInteraction.__name__ = !0;
    b.SocialInteraction.prototype = {
        network: null,
        action: null,
        target: null,
        validate: function() {
            null != this.network && null != this.action || b.Tracker._raiseError('Social interactions need to have at least the "network" and "action" attributes defined.',
                "SocialInteraction.validate")
        },
        setNetwork: function(a) {
            this.network = a
        },
        getNetwork: function() {
            return this.network
        },
        setAction: function(a) {
            this.action = a
        },
        getAction: function() {
            return this.action
        },
        setTarget: function(a) {
            this.target = a
        },
        getTarget: function() {
            return this.target
        },
        __class__: b.SocialInteraction
    };
    b.Stats = function() {};
    b.Stats.__name__ = !0;
    b.Stats.init = function(a, c) {
        null == b.Stats.accountId && (b.Stats.accountId = a, b.Stats.domainName = c, b.Stats.tracker = new b.Tracker(a, c), b.Stats.cache = new q.ds.StringMap,
            b.Stats.session = new b.Session, b.Stats.loadVisitor())
    };
    b.Stats.trackPageview = function(a, c) {
        var r = "page:" + a;
        if (!b.Stats.cache.exists(r)) {
            var e = new b.Page(a);
            null != c && e.setTitle(c);
            e = new b._Stats.GATrackObject(e, null);
            b.Stats.cache.set(r, e)
        }
        b.Stats.track(r)
    };
    b.Stats.trackEvent = function(a, c, r, e) {
        null == e && (e = 0);
        var f = "event:" + a + "/" + c + "/" + r + ":" + e;
        b.Stats.cache.exists(f) || (a = new b._Stats.GATrackObject(null, new b.Event(a, c, r, e)), b.Stats.cache.set(f, a));
        b.Stats.track(f)
    };
    b.Stats.track = function(a) {
        b.Stats.cache.get(a).track(b.Stats.tracker,
            b.Stats.visitor, b.Stats.session);
        b.Stats.persistVisitor()
    };
    b.Stats.loadVisitor = function() {
        b.Stats.visitor = new b.Visitor;
        b.Stats.visitor.setUserAgent("-not-set- [haxe]");
        b.Stats.visitor.setScreenResolution("1024x768");
        b.Stats.visitor.setLocale("en_US");
        b.Stats.visitor.getUniqueId();
        b.Stats.visitor.addSession(b.Stats.session);
        b.Stats.persistVisitor()
    };
    b.Stats.persistVisitor = function() {};
    b._Stats = {};
    b._Stats.GATrackObject = function(a, c) {
        this.page = a;
        this.event = c
    };
    b._Stats.GATrackObject.__name__ = !0;
    b._Stats.GATrackObject.prototype = {
        event: null,
        page: null,
        track: function(a, c, b) {
            null != this.page && a.trackPageview(this.page, b, c);
            null != this.event && a.trackEvent(this.event, b, c)
        },
        __class__: b._Stats.GATrackObject
    };
    b.Tracker = function(a, c, r) {
        this.allowHash = !0;
        this.customVariables = [];
        b.Tracker.setConfig(null != r ? r : new b.Config);
        this.setAccountId(a);
        this.setDomainName(c)
    };
    b.Tracker.__name__ = !0;
    b.Tracker.getConfig = function() {
        return b.Tracker.config
    };
    b.Tracker.setConfig = function(a) {
        b.Tracker.config = a
    };
    b.Tracker._raiseError = function(a, c) {
        a = c + "(): " +
        a;
        switch (null != b.Tracker.config ? b.Tracker.config.getErrorSeverity() : 0) {
            case 1:
                console.log(a);
                break;
            case 2:
                throw a;
        }
    };
    b.Tracker.prototype = {
        accountId: null,
        domainName: null,
        allowHash: null,
        customVariables: null,
        campaign: null,
        setAccountId: function(a) {
            (new s("^(UA|MO)-[0-9]*-[0-9]*$", "")).match(a) || b.Tracker._raiseError('"' + a + '" is not a valid Google Analytics account ID.', "Tracker.setAccountId");
            this.accountId = a
        },
        getAccountId: function() {
            return this.accountId
        },
        setDomainName: function(a) {
            this.domainName = a
        },
        getDomainName: function() {
            return this.domainName
        },
        setAllowHash: function(a) {
            this.allowHash = a
        },
        getAllowHash: function() {
            return this.allowHash
        },
        addCustomVariable: function(a) {
            a.validate();
            this.customVariables[a.getIndex()] = a
        },
        getCustomVariables: function() {
            return this.customVariables
        },
        removeCustomVariable: function(a) {
            m.remove(this.customVariables, this.customVariables[a])
        },
        setCampaign: function(a) {
            null != a && a.validate();
            this.campaign = a
        },
        getCampaign: function() {
            return this.campaign
        },
        trackPageview: function(a,
                                c, r) {
            var e = new b.internals.request.PageviewRequest(b.Tracker.config);
            e.setPage(a);
            e.setSession(c);
            e.setVisitor(r);
            e.setTracker(this);
            e.send()
        },
        trackEvent: function(a, c, r) {
            a.validate();
            var e = new b.internals.request.EventRequest(b.Tracker.config);
            e.setEvent(a);
            e.setSession(c);
            e.setVisitor(r);
            e.setTracker(this);
            e.send()
        },
        trackTransaction: function(a, c, r) {
            a.validate();
            var e = new b.internals.request.TransactionRequest(b.Tracker.config);
            e.setTransaction(a);
            e.setSession(c);
            e.setVisitor(r);
            e.setTracker(this);
            e.send();
            for (a = a.getItems().iterator(); a.hasNext();) {
                e = a.next();
                e.validate();
                var f = new b.internals.request.ItemRequest(b.Tracker.config);
                f.setItem(e);
                f.setSession(c);
                f.setVisitor(r);
                f.setTracker(this);
                f.send()
            }
        },
        trackSocial: function(a, c, r, e) {
            var f = new b.internals.request.SocialInteractionRequest(b.Tracker.config);
            f.setSocialInteraction(a);
            f.setPage(c);
            f.setSession(r);
            f.setVisitor(e);
            f.setTracker(this);
            f.send()
        },
        __class__: b.Tracker
    };
    b.Transaction = function() {
        this.items = new q.ds.StringMap
    };
    b.Transaction.__name__ = !0;
    b.Transaction.prototype = {
        orderId: null,
        affiliation: null,
        total: null,
        tax: null,
        shipping: null,
        city: null,
        region: null,
        country: null,
        items: null,
        validate: function() {
            null == this.items && b.Tracker._raiseError("Transactions need to consist of at least one item.", "Transaction.validate")
        },
        addItem: function(a) {
            a.setOrderId(this.orderId);
            var c = a.getSku();
            this.items.set(c, a)
        },
        getItems: function() {
            return this.items
        },
        getOrderId: function() {
            return this.orderId
        },
        setOrderId: function(a) {
            this.orderId = a;
            for (var c = this.items.iterator(); c.hasNext();) c.next().setOrderId(a)
        },
        getAffiliation: function() {
            return this.affiliation
        },
        setAffiliation: function(a) {
            this.affiliation = a
        },
        getTotal: function() {
            return this.total
        },
        setTotal: function(a) {
            this.total = a
        },
        getTax: function() {
            return this.tax
        },
        setTax: function(a) {
            this.tax = a
        },
        getShipping: function() {
            return this.shipping
        },
        setShipping: function(a) {
            this.shipping = a
        },
        getCity: function() {
            return this.city
        },
        setCity: function(a) {
            this.city = a
        },
        getRegion: function() {
            return this.region
        },
        setRegion: function(a) {
            this.region = a
        },
        getCountry: function() {
            return this.country
        },
        setCountry: function(a) {
            this.country = a
        },
        __class__: b.Transaction
    };
    b.URLParser = function(a) {
        this.url = a;
        var c = new s("^(?:(?![^:@]+:[^:@/]*@)([^:/?#.]+):)?(?://)?((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:/?#]*)(?::(\\d*))?)(((/(?:[^?#](?![^?#/]*\\.[^?#/.]+(?:[?#]|$)))*/?)?([^?#/]*))(?:\\?([^#]*))?(?:#(.*))?)", "");
        c.match(a);
        a = 0;
        for (var r = b.URLParser.parts.length; a < r;) {
            var e = a++;
            v.setField(this, b.URLParser.parts[e], c.matched(e))
        }
    };
    b.URLParser.__name__ = !0;
    b.URLParser.parse = function(a) {
        return new b.URLParser(a)
    };
    b.URLParser.prototype = {
        url: null,
        source: null,
        protocol: null,
        authority: null,
        userInfo: null,
        user: null,
        password: null,
        host: null,
        port: null,
        relative: null,
        path: null,
        directory: null,
        file: null,
        query: null,
        anchor: null,
        toString: function() {
            for (var a = "For Url -> " + this.url + "\n", c = 0, r = b.URLParser.parts.length; c < r;) var e = c++,
                a = a + (b.URLParser.parts[e] + ": " + y.string(v.field(this, b.URLParser.parts[e])) + (e == b.URLParser.parts.length - 1 ? "" : "\n"));
            return a
        },
        __class__: b.URLParser
    };
    b.Visitor = function() {
        var a = new b.DateTime;
        this.uniqueId =
            0;
        this.setFirstVisitTime(a);
        this.setPreviousVisitTime(a);
        this.setCurrentVisitTime(a);
        this.setVisitCount(1)
    };
    b.Visitor.__name__ = !0;
    b.Visitor.prototype = {
        uniqueId: null,
        firstVisitTime: null,
        previousVisitTime: null,
        currentVisitTime: null,
        visitCount: null,
        ipAddress: null,
        userAgent: null,
        locale: null,
        flashVersion: null,
        javaEnabled: null,
        screenColorDepth: null,
        screenResolution: null,
        fromUtma: function(a) {
            a = a.split(".");
            if (6 != a.length) return b.Tracker._raiseError('The given "__utma" cookie value is invalid.', "Visitor.fromUtma"),
                this;
            this.setUniqueId(b.internals.Util.parseInt(a[1], 0));
            this.setFirstVisitTime(new b.DateTime(a[2]));
            this.setPreviousVisitTime(new b.DateTime(a[3]));
            this.setCurrentVisitTime(new b.DateTime(a[4]));
            this.setVisitCount(b.internals.Util.parseInt(a[5], 0));
            return this
        },
        generateHash: function() {
            return b.internals.Util.generateHash(this.userAgent + this.screenResolution + this.screenColorDepth)
        },
        generateUniqueId: function() {
            return (b.internals.Util.generate32bitRandom() ^ this.generateHash()) & 2147483647
        },
        setUniqueId: function(a) {
            (0 >
            a || 2147483647 < a) && b.Tracker._raiseError("Visitor unique ID has to be a 32-bit integer between 0 and 2147483647.", "Visitor.setUniqueId");
            this.uniqueId = a
        },
        getUniqueId: function() {
            0 == this.uniqueId && (this.uniqueId = this.generateUniqueId());
            return this.uniqueId
        },
        addSession: function(a) {
            a = a.getStartTime();
            a != this.currentVisitTime && (this.previousVisitTime = this.currentVisitTime, this.currentVisitTime = a, ++this.visitCount)
        },
        setFirstVisitTime: function(a) {
            this.firstVisitTime = a
        },
        getFirstVisitTime: function() {
            return this.firstVisitTime
        },
        setPreviousVisitTime: function(a) {
            this.previousVisitTime = a
        },
        getPreviousVisitTime: function() {
            return this.previousVisitTime
        },
        setCurrentVisitTime: function(a) {
            this.currentVisitTime = a
        },
        getCurrentVisitTime: function() {
            return this.currentVisitTime
        },
        setVisitCount: function(a) {
            this.visitCount = a
        },
        getVisitCount: function() {
            return this.visitCount
        },
        setIpAddress: function(a) {
            this.ipAddress = a
        },
        getIpAddress: function() {
            return this.ipAddress
        },
        setUserAgent: function(a) {
            this.userAgent = a
        },
        getUserAgent: function() {
            return this.userAgent
        },
        setLocale: function(a) {
            this.locale = a
        },
        getLocale: function() {
            return this.locale
        },
        setFlashVersion: function(a) {
            this.flashVersion = a
        },
        getFlashVersion: function() {
            return this.flashVersion
        },
        setJavaEnabled: function(a) {
            this.javaEnabled = a
        },
        getJavaEnabled: function() {
            return this.javaEnabled
        },
        setScreenColorDepth: function(a) {
            this.screenColorDepth = a
        },
        getScreenColorDepth: function() {
            return this.screenColorDepth
        },
        setScreenResolution: function(a) {
            this.screenResolution = a
        },
        getScreenResolution: function() {
            return this.screenResolution
        },
        __class__: b.Visitor
    };
    b.internals = {};
    b.internals.ParameterHolder = function() {
        this.utmwv = "5.2.5";
        this.utmr = this.utmcs = this.utmfl = this.utmje = "0"
    };
    b.internals.ParameterHolder.__name__ = !0;
    b.internals.ParameterHolder.prototype = {
        utmwv: null,
        utmac: null,
        utmhn: null,
        utmvid: null,
        utmt: null,
        utms: null,
        utmn: null,
        utmcc: null,
        utme: null,
        utmni: null,
        utmu: null,
        utmp: null,
        utmdt: null,
        utmcs: null,
        utmr: null,
        utmip: null,
        utmul: null,
        utmfl: null,
        utmje: null,
        utmsc: null,
        utmsr: null,
        __utma: null,
        utmhid: null,
        __utmb: null,
        __utmc: null,
        utmipc: null,
        utmipn: null,
        utmipr: null,
        utmiqt: null,
        utmiva: null,
        utmtid: null,
        utmtst: null,
        utmtto: null,
        utmttx: null,
        utmtsp: null,
        utmtci: null,
        utmtrg: null,
        utmtco: null,
        utmcn: null,
        utmcr: null,
        utmcid: null,
        utmcsr: null,
        utmgclid: null,
        utmdclid: null,
        utmccn: null,
        utmcmd: null,
        utmctr: null,
        utmcct: null,
        utmcvr: null,
        __utmz: null,
        utmsn: null,
        utmsa: null,
        utmsid: null,
        __utmx: null,
        __utmv: null,
        toHashTable: function() {
            for (var a = new q.ds.StringMap, c = 0, r = D.getInstanceFields(b.internals.ParameterHolder); c < r.length;) {
                var e = r[c];
                ++c;
                if ("_" != e.charAt(0) &&
                    !v.isFunction(v.field(this, e))) {
                    var f = v.field(this, e);
                    a.set(e, f)
                }
            }
            return a
        },
        toQueryString: function() {
            for (var a = "", c = 0, r = D.getInstanceFields(b.internals.ParameterHolder); c < r.length;) {
                var e = r[c];
                ++c;
                "_" == e.charAt(0) || v.isFunction(v.field(this, e)) || null == v.field(this, e) || "null" == v.field(this, e) || (a += e + "=" + t.replace(y.string(v.field(this, e)) + "", "&", "%26") + "&")
            }
            return a
        },
        __class__: b.internals.ParameterHolder
    };
    b.internals.Util = function() {};
    b.internals.Util.__name__ = !0;
    b.internals.Util.encodeUriComponent =
        function(a) {
            return b.internals.Util.convertToUriComponentEncoding(t.urlEncode(a))
        };
    b.internals.Util.stringReplaceArray = function(a, c, b) {
        for (var e = 0, f = c.length; e < f;) {
            var d = e++;
            null != c[d] && (a = t.replace(a + " ", c[d], b[d]))
        }
        return t.trim(a)
    };
    b.internals.Util.parseInt = function(a, c) {
        return null == a ? c : y.parseInt(a)
    };
    b.internals.Util.convertToUriComponentEncoding = function(a) {
        return b.internals.Util.stringReplaceArray(a, "!*'() +".split(""), "%21 %2A %27 %28 %29 %20 %20".split(" "))
    };
    b.internals.Util.generate32bitRandom =
        function() {
            return Math.round(2147483647 * Math.random())
        };
    b.internals.Util.generateHash = function(a) {
        var c = 1,
            b;
        if (null != a && "" != a)
            for (var c = 0, e = a.length - 1; 0 <= e;) b = m.cca(a, e), c = (c << 6 & 268435455) + b + (b << 14), b = c & 266338304, 0 != b && (c ^= b >> 21), e--;
        return c
    };
    b.internals.X10 = function() {
        this.projectData = new q.ds.StringMap;
        this.KEY = "k";
        this.VALUE = "v";
        this.SET = ["k", "v"];
        this.DELIM_BEGIN = "(";
        this.DELIM_END = ")";
        this.DELIM_SET = "*";
        this.DELIM_NUM_VALUE = "!";
        this.MINIMUM = 1;
        this.ESCAPE_CHAR_MAP = new q.ds.StringMap;
        this.ESCAPE_CHAR_MAP.set("'",
            "'0");
        this.ESCAPE_CHAR_MAP.set(")", "'1");
        this.ESCAPE_CHAR_MAP.set("*", "'2");
        this.ESCAPE_CHAR_MAP.set("!", "'3")
    };
    b.internals.X10.__name__ = !0;
    b.internals.X10.prototype = {
        projectData: null,
        KEY: null,
        VALUE: null,
        SET: null,
        DELIM_BEGIN: null,
        DELIM_END: null,
        DELIM_SET: null,
        DELIM_NUM_VALUE: null,
        ESCAPE_CHAR_MAP: null,
        MINIMUM: null,
        hasProject: function(a) {
            return this.projectData.exists(a)
        },
        setKey: function(a, c, b) {
            this.setInternal(a, this.KEY, c, b)
        },
        getKey: function(a, c) {
            return this.getInternal(a, this.KEY, c)
        },
        clearKey: function(a) {
            this.clearInternal(a,
                this.KEY)
        },
        setValue: function(a, c, b) {
            this.setInternal(a, this.VALUE, c, b)
        },
        getValue: function(a, c) {
            return this.getInternal(a, this.VALUE, c)
        },
        clearValue: function(a) {
            this.clearInternal(a, this.VALUE)
        },
        setInternal: function(a, c, b, e) {
            if (!this.projectData.exists(a)) {
                var f = new q.ds.StringMap;
                this.projectData.set(a, f)
            }
            a = this.projectData.get(a);
            a.exists(c) || a.set(c, []);
            a.get(c)[b] = e
        },
        getInternal: function(a, c, b) {
            if (!this.projectData.exists(a)) return null;
            a = this.projectData.get(a);
            if (!a.exists(c)) return null;
            c = a.get(c);
            return null == c[b] ? null : c[b]
        },
        clearInternal: function(a, c) {
            var b;
            if (b = this.projectData.exists(a)) b = this.projectData.get(a).exists(c);
            b && this.projectData.get(a).remove(c)
        },
        escapeExtensibleValue: function(a) {
            for (var c = "", b = 0, e = a.length; b < e;) var f = b++,
                f = a.charAt(f),
                c = this.ESCAPE_CHAR_MAP.exists(f) ? c + this.ESCAPE_CHAR_MAP.get(f) : c + f;
            return c
        },
        SORT_NUMERIC: function(a, c) {
            return a == c ? 0 : a > c ? 1 : -1
        },
        renderDataType: function(a) {
            for (var c = [], b = 0, e = 0, f = a.length; e < f;) {
                var d = e++,
                    g = a[d];
                if (null != g) {
                    var k = "";
                    d != this.MINIMUM &&
                    d - 1 != b && (k += d, k += this.DELIM_NUM_VALUE);
                    k += this.escapeExtensibleValue(g);
                    c.push(k)
                }
                b = d
            }
            return this.DELIM_BEGIN + c.join(this.DELIM_SET) + this.DELIM_END
        },
        renderProject: function(a) {
            for (var c = "", b = !1, e = 0, f = this.SET.length; e < f;) {
                var d = e++;
                a.exists(this.SET[d]) ? (b && (c += this.SET[d]), c += this.renderDataType(a.get(this.SET[d])), b = !1) : b = !0
            }
            return c
        },
        renderUrlString: function() {
            for (var a = "", c = this.projectData.keys(); c.hasNext();) var b = c.next(),
                a = a + (b + this.renderProject(this.projectData.get(b)));
            return a
        },
        __class__: b.internals.X10
    };
    b.internals.request = {};
    b.internals.request.Request = function(a) {
        this.setConfig(null != a ? a : new b.Config)
    };
    b.internals.request.Request.__name__ = !0;
    b.internals.request.Request.onError = function(a) {};
    b.internals.request.Request.prototype = {
        type: null,
        config: null,
        userAgent: null,
        tracker: null,
        visitor: null,
        session: null,
        getConfig: function() {
            return this.config
        },
        setConfig: function(a) {
            this.config = a
        },
        setUserAgent: function(a) {
            this.userAgent = a
        },
        getTracker: function() {
            return this.tracker
        },
        setTracker: function(a) {
            this.tracker =
                a
        },
        getVisitor: function() {
            return this.visitor
        },
        setVisitor: function(a) {
            this.visitor = a
        },
        getSession: function() {
            return this.session
        },
        setSession: function(a) {
            this.session = a
        },
        increaseTrackCount: function() {
            this.session.increaseTrackCount();
            500 < this.session.getTrackCount() && b.Tracker._raiseError("Google Analytics does not guarantee to process more than 500 requests per session.", "Request.buildHttpRequest");
            null != this.tracker.getCampaign() && this.tracker.getCampaign().increaseResponseCount()
        },
        send: function() {
            if (null !=
                this.config.getEndPointHost()) {
                var a = this.buildParameters();
                null != this.visitor && (this.setUserAgent(this.visitor.getUserAgent()), a.utmvid = this.visitor.getUniqueId());
                a = b.internals.Util.convertToUriComponentEncoding(a.toQueryString());
                a = this.config.getUrlScheme() + "://" + this.config.getEndPointHost() + this.config.getEndPointPath() + "?" + a;
                this.increaseTrackCount();
                (new Image).src = a
            }
        },
        getType: function() {
            return null
        },
        buildParameters: function() {
            var a = new b.internals.ParameterHolder;
            a.utmac = this.tracker.getAccountId();
            a.utmhn = this.tracker.getDomainName();
            a.utmt = "" + this.getType();
            a.utmn = b.internals.Util.generate32bitRandom();
            a.utmip = this.visitor.getIpAddress();
            a.utmhid = this.session.getSessionId();
            a.utms = this.session.getTrackCount();
            a = this.buildVisitorParameters(a);
            a = this.buildCustomVariablesParameter(a);
            a = this.buildCampaignParameters(a);
            return a = this.buildCookieParameters(a)
        },
        buildVisitorParameters: function(a) {
            null != this.visitor.getLocale() && (a.utmul = t.replace(this.visitor.getLocale(), "_", "-").toLowerCase());
            null !=
            this.visitor.getFlashVersion() && (a.utmfl = this.visitor.getFlashVersion());
            this.visitor.getJavaEnabled() ? a.utmje = "1" : a.utmje = "0";
            null != this.visitor.getScreenColorDepth() && (a.utmsc = this.visitor.getScreenColorDepth() + "-bit");
            a.utmsr = this.visitor.getScreenResolution();
            return a
        },
        buildCustomVariablesParameter: function(a) {
            var c = this.tracker.getCustomVariables();
            if (null == c) return a;
            5 < c.length && b.Tracker._raiseError("The sum of all custom variables cannot exceed 5 in any given request.", "Request.buildCustomVariablesParameter");
            var r = new b.internals.X10,
                e, f;
            r.clearKey("8");
            r.clearKey("9");
            r.clearKey("11");
            for (var d = 0; d < c.length;) {
                var g = c[d];
                ++d;
                e = b.internals.Util.encodeUriComponent(g.getName());
                f = b.internals.Util.encodeUriComponent(g.getValue());
                r.setKey("8", g.getIndex(), e);
                r.setKey("9", g.getIndex(), f);
                3 != g.getScope() && r.setKey("11", g.getIndex(), g.getScope())
            }
            c = r.renderUrlString();
            null != c && (a.utme = null == a.utme ? c : a.utme + c);
            return a
        },
        buildCookieParameters: function(a) {
            var c = this.generateDomainHash();
            a.__utma = c + ".";
            a.__utma +=
                this.visitor.getUniqueId() + ".";
            a.__utma += this.visitor.getFirstVisitTime().toString() + ".";
            a.__utma += this.visitor.getPreviousVisitTime().toString() + ".";
            a.__utma += this.visitor.getCurrentVisitTime().toString() + ".";
            a.__utma += this.visitor.getVisitCount();
            a.__utmb = c + ".";
            a.__utmb += this.session.getTrackCount() + ".";
            a.__utmb += "10.";
            a.__utmb += this.session.getStartTime().toString();
            a.__utmc = c;
            c = "__utma=" + a.__utma + ";";
            null != a.__utmz && (c += "+__utmz=" + a.__utmz + ";");
            null != a.__utmv && (c += "+__utmv=" + a.__utmv + ";");
            a.utmcc =
                c;
            return a
        },
        buildCampaignParameters: function(a) {
            var c = this.tracker.getCampaign();
            if (null == c) return a;
            a.__utmz = this.generateDomainHash() + ".";
            a.__utmz += c.getCreationTime().toString() + ".";
            a.__utmz += this.visitor.getVisitCount() + ".";
            a.__utmz += c.getResponseCount() + ".";
            c = "utmcid=" + c.getId() + "|utmcsr=" + c.getSource() + "|utmgclid=" + c.getGClickId() + "|utmdclid=" + c.getDClickId() + "|utmccn=" + c.getName() + "|utmcmd=" + c.getMedium() + "|utmctr=" + c.getTerm() + "|utmcct=" + c.getContent();
            a.__utmz += t.replace(t.replace(c, "+",
                "%20"), " ", "%20");
            return a
        },
        generateDomainHash: function() {
            var a = 1;
            this.tracker.getAllowHash() && (a = b.internals.Util.generateHash(this.tracker.getDomainName()));
            return a
        },
        __class__: b.internals.request.Request
    };
    b.internals.request.EventRequest = function(a) {
        b.internals.request.Request.call(this, a)
    };
    b.internals.request.EventRequest.__name__ = !0;
    b.internals.request.EventRequest.__super__ = b.internals.request.Request;
    b.internals.request.EventRequest.prototype = w(b.internals.request.Request.prototype, {
        event: null,
        getType: function() {
            return "event"
        },
        buildParameters: function() {
            var a = b.internals.request.Request.prototype.buildParameters.call(this),
                c = new b.internals.X10;
            c.clearKey("5");
            c.clearValue("5");
            c.setKey("5", 1, this.event.getCategory());
            c.setKey("5", 2, this.event.getAction());
            null != this.event.getLabel() && c.setKey("5", 3, this.event.getLabel());
            0 != this.event.getValue() && c.setValue("5", 1, this.event.getValue());
            c = c.renderUrlString();
            null != c && (a.utme = null == a.utme ? c : a.utme + c);
            this.event.getNoninteraction() && (a.utmni =
                1);
            return a
        },
        getEvent: function() {
            return this.event
        },
        setEvent: function(a) {
            this.event = a
        },
        __class__: b.internals.request.EventRequest
    });
    b.internals.request.ItemRequest = function(a) {
        b.internals.request.Request.call(this, a)
    };
    b.internals.request.ItemRequest.__name__ = !0;
    b.internals.request.ItemRequest.__super__ = b.internals.request.Request;
    b.internals.request.ItemRequest.prototype = w(b.internals.request.Request.prototype, {
        item: null,
        getType: function() {
            return "item"
        },
        buildParameters: function() {
            var a = b.internals.request.Request.prototype.buildParameters.call(this);
            a.utmtid = this.item.getOrderId();
            a.utmipc = this.item.getSku();
            a.utmipn = this.item.getName();
            a.utmiva = this.item.getVariation();
            a.utmipr = this.item.getPrice();
            a.utmiqt = this.item.getQuantity();
            return a
        },
        buildVisitorParameters: function(a) {
            return a
        },
        buildCustomVariablesParameter: function(a) {
            return a
        },
        getItem: function() {
            return this.item
        },
        setItem: function(a) {
            this.item = a
        },
        __class__: b.internals.request.ItemRequest
    });
    b.internals.request.PageviewRequest = function(a) {
        b.internals.request.Request.call(this, a)
    };
    b.internals.request.PageviewRequest.__name__ = !0;
    b.internals.request.PageviewRequest.__super__ = b.internals.request.Request;
    b.internals.request.PageviewRequest.prototype = w(b.internals.request.Request.prototype, {
        page: null,
        getType: function() {
            return null
        },
        buildParameters: function() {
            var a = b.internals.request.Request.prototype.buildParameters.call(this);
            a.utmp = this.page.getPath();
            a.utmdt = this.page.getTitle();
            null != this.page.getCharset() && (a.utmcs = this.page.getCharset());
            null != this.page.getReferrer() && (a.utmr = this.page.getReferrer());
            0 != this.page.getLoadTime() &&
            a.utmn % 100 < this.config.getSitespeedSampleRate() && (a.utme = null == a.utme ? "0" : a.utme + 0);
            return a
        },
        getPage: function() {
            return this.page
        },
        setPage: function(a) {
            this.page = a
        },
        __class__: b.internals.request.PageviewRequest
    });
    b.internals.request.SocialInteractionRequest = function(a) {
        b.internals.request.PageviewRequest.call(this, a)
    };
    b.internals.request.SocialInteractionRequest.__name__ = !0;
    b.internals.request.SocialInteractionRequest.__super__ = b.internals.request.PageviewRequest;
    b.internals.request.SocialInteractionRequest.prototype =
        w(b.internals.request.PageviewRequest.prototype, {
            socialInteraction: null,
            getType: function() {
                return "social"
            },
            buildParameters: function() {
                var a = b.internals.request.PageviewRequest.prototype.buildParameters.call(this);
                a.utmsn = this.socialInteraction.getNetwork();
                a.utmsa = this.socialInteraction.getAction();
                a.utmsid = this.socialInteraction.getTarget();
                null == a.utmsid && (a.utmsid = this.page.getPath());
                return a
            },
            getSocialInteraction: function() {
                return this.socialInteraction
            },
            setSocialInteraction: function(a) {
                this.socialInteraction =
                    a
            },
            __class__: b.internals.request.SocialInteractionRequest
        });
    b.internals.request.TransactionRequest = function(a) {
        b.internals.request.Request.call(this, a)
    };
    b.internals.request.TransactionRequest.__name__ = !0;
    b.internals.request.TransactionRequest.__super__ = b.internals.request.Request;
    b.internals.request.TransactionRequest.prototype = w(b.internals.request.Request.prototype, {
        transaction: null,
        getType: function() {
            return "tran"
        },
        buildParameters: function() {
            var a = b.internals.request.Request.prototype.buildParameters.call(this);
            a.utmtid = this.transaction.getOrderId();
            a.utmtst = this.transaction.getAffiliation();
            a.utmtto = this.transaction.getTotal();
            a.utmttx = this.transaction.getTax();
            a.utmtsp = this.transaction.getShipping();
            a.utmtci = this.transaction.getCity();
            a.utmtrg = this.transaction.getRegion();
            a.utmtco = this.transaction.getCountry();
            return a
        },
        buildVisitorParameters: function(a) {
            return a
        },
        buildCustomVariablesParameter: function(a) {
            return a
        },
        getTransaction: function() {
            return this.transaction
        },
        setTransaction: function(a) {
            this.transaction =
                a
        },
        __class__: b.internals.request.TransactionRequest
    });
    var q = {
        Http: function(a) {
            this.url = a;
            this.headers = new A;
            this.params = new A;
            this.async = !0
        }
    };
    q.Http.__name__ = !0;
    q.Http.requestUrl = function(a) {
        a = new q.Http(a);
        a.async = !1;
        var c = null;
        a.onData = function(a) {
            c = a
        };
        a.onError = function(a) {
            throw a;
        };
        a.request(!1);
        return c
    };
    q.Http.prototype = {
        url: null,
        responseData: null,
        async: null,
        postData: null,
        headers: null,
        params: null,
        req: null,
        request: function(a) {
            var c = this;
            c.responseData = null;
            var b = this.req = u.Browser.createXMLHttpRequest(),
                e = function(a) {
                    if (4 == b.readyState) {
                        var e;
                        try {
                            e = b.status
                        } catch (f) {
                            e = null
                        }
                        void 0 == e && (e = null);
                        if (null != e) c.onStatus(e);
                        if (null != e && 200 <= e && 400 > e) c.req = null, c.onData(c.responseData = b.responseText);
                        else if (null == e) c.req = null, c.onError("Failed to connect or resolve host");
                        else switch (e) {
                                case 12029:
                                    c.req = null;
                                    c.onError("Failed to connect to host");
                                    break;
                                case 12007:
                                    c.req = null;
                                    c.onError("Unknown host");
                                    break;
                                default:
                                    c.req = null, c.responseData = b.responseText, c.onError("Http Error #" + b.status)
                            }
                    }
                };
            this.async &&
            (b.onreadystatechange = e);
            var f = this.postData;
            if (null != f) a = !0;
            else
                for (var d = this.params.iterator(); d.hasNext();) var g = d.next(),
                    f = null == f ? "" : f + "&",
                    f = f + (encodeURIComponent(g.param) + "=" + encodeURIComponent(g.value));
            try {
                if (a) b.open("POST", this.url, this.async);
                else if (null != f) {
                    var k = 1 >= this.url.split("?").length;
                    b.open("GET", this.url + (k ? "?" : "&") + f, this.async);
                    f = null
                } else b.open("GET", this.url, this.async)
            } catch (h) {
                c.req = null;
                this.onError(h.toString());
                return
            }!x.exists(this.headers, function(a) {
                return "Content-Type" ==
                    a.header
            }) && a && null == this.postData && b.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            for (a = this.headers.iterator(); a.hasNext();) d = a.next(), b.setRequestHeader(d.header, d.value);
            b.send(f);
            this.async || e(null)
        },
        onData: function(a) {},
        onError: function(a) {},
        onStatus: function(a) {},
        __class__: q.Http
    };
    q.Timer = function(a) {
        var c = this;
        this.id = setInterval(function() {
            c.run()
        }, a)
    };
    q.Timer.__name__ = !0;
    q.Timer.delay = function(a, c) {
        var b = new q.Timer(c);
        b.run = function() {
            b.stop();
            a()
        };
        return b
    };
    q.Timer.prototype = {
        id: null,
        stop: function() {
            null != this.id && (clearInterval(this.id), this.id = null)
        },
        run: function() {},
        __class__: q.Timer
    };
    q.ds = {};
    q.ds.StringMap = function() {
        this.h = {}
    };
    q.ds.StringMap.__name__ = !0;
    q.ds.StringMap.__interfaces__ = [z];
    q.ds.StringMap.prototype = {
        h: null,
        set: function(a, c) {
            this.h["$" + a] = c
        },
        get: function(a) {
            return this.h["$" + a]
        },
        exists: function(a) {
            return this.h.hasOwnProperty("$" + a)
        },
        remove: function(a) {
            a = "$" + a;
            if (!this.h.hasOwnProperty(a)) return !1;
            delete this.h[a];
            return !0
        },
        keys: function() {
            var a = [],
                c;
            for (c in this.h) this.h.hasOwnProperty(c) && a.push(c.substr(1));
            return m.iter(a)
        },
        iterator: function() {
            return {
                ref: this.h,
                it: this.keys(),
                hasNext: function() {
                    return this.it.hasNext()
                },
                next: function() {
                    var a = this.it.next();
                    return this.ref["$" + a]
                }
            }
        },
        __class__: q.ds.StringMap
    };
    q.io = {};
    q.io.Eof = function() {};
    q.io.Eof.__name__ = !0;
    q.io.Eof.prototype = {
        toString: function() {
            return "Eof"
        },
        __class__: q.io.Eof
    };
    q.xml = {};
    q.xml.Parser = function() {};
    q.xml.Parser.__name__ = !0;
    q.xml.Parser.parse = function(a) {
        var c = h.createDocument();
        q.xml.Parser.doParse(a, 0, c);
        return c
    };
    q.xml.Parser.doParse = function(a, c, b) {
        null == c && (c = 0);
        for (var e = null, f = 1, d = 1, g = null, k = 0, p = 0, n = 0, l = a.charCodeAt(c), s = new C; l == l;) {
            switch (f) {
                case 0:
                    switch (l) {
                        case 10:
                        case 13:
                        case 9:
                        case 32:
                            break;
                        default:
                            f = d;
                            continue
                    }
                    break;
                case 1:
                    switch (l) {
                        case 60:
                            f = 0;
                            d = 2;
                            break;
                        default:
                            k = c;
                            f = 13;
                            continue
                    }
                    break;
                case 13:
                    60 == l ? (d = h.createPCData(s.b + m.substr(a, k, c - k)), s = new C, b.addChild(d), p++, f = 0, d = 2) : 38 == l && (s.addSub(a, k, c - k), f = 18, d = 13, k = c + 1);
                    break;
                case 17:
                    93 == l && 93 == a.charCodeAt(c + 1) &&
                    62 == a.charCodeAt(c + 2) && (f = h.createCData(m.substr(a, k, c - k)), b.addChild(f), p++, c += 2, f = 1);
                    break;
                case 2:
                    switch (l) {
                        case 33:
                            if (91 == a.charCodeAt(c + 1)) {
                                c += 2;
                                if ("CDATA[" != m.substr(a, c, 6).toUpperCase()) throw "Expected <![CDATA[";
                                c += 5;
                                f = 17
                            } else if (68 == a.charCodeAt(c + 1) || 100 == a.charCodeAt(c + 1)) {
                                if ("OCTYPE" != m.substr(a, c + 2, 6).toUpperCase()) throw "Expected <!DOCTYPE";
                                c += 8;
                                f = 16
                            } else {
                                if (45 != a.charCodeAt(c + 1) || 45 != a.charCodeAt(c + 2)) throw "Expected \x3c!--";
                                c += 2;
                                f = 15
                            }
                            k = c + 1;
                            break;
                        case 63:
                            f = 14;
                            k = c;
                            break;
                        case 47:
                            if (null ==
                                b) throw "Expected node name";
                            k = c + 1;
                            f = 0;
                            d = 10;
                            break;
                        default:
                            f = 3;
                            k = c;
                            continue
                    }
                    break;
                case 3:
                    if (!(97 <= l && 122 >= l || 65 <= l && 90 >= l || 48 <= l && 57 >= l || 58 == l || 46 == l || 95 == l || 45 == l)) {
                        if (c == k) throw "Expected node name";
                        e = h.createElement(m.substr(a, k, c - k));
                        b.addChild(e);
                        f = 0;
                        d = 4;
                        continue
                    }
                    break;
                case 4:
                    switch (l) {
                        case 47:
                            f = 11;
                            p++;
                            break;
                        case 62:
                            f = 9;
                            p++;
                            break;
                        default:
                            f = 5;
                            k = c;
                            continue
                    }
                    break;
                case 5:
                    if (!(97 <= l && 122 >= l || 65 <= l && 90 >= l || 48 <= l && 57 >= l || 58 == l || 46 == l || 95 == l || 45 == l)) {
                        if (k == c) throw "Expected attribute name";
                        g = m.substr(a,
                            k, c - k);
                        if (e.exists(g)) throw "Duplicate attribute";
                        f = 0;
                        d = 6;
                        continue
                    }
                    break;
                case 6:
                    switch (l) {
                        case 61:
                            f = 0;
                            d = 7;
                            break;
                        default:
                            throw "Expected =";
                    }
                    break;
                case 7:
                    switch (l) {
                        case 34:
                        case 39:
                            f = 8;
                            k = c;
                            break;
                        default:
                            throw 'Expected "';
                    }
                    break;
                case 8:
                    l == a.charCodeAt(k) && (d = m.substr(a, k + 1, c - k - 1), e.set(g, d), f = 0, d = 4);
                    break;
                case 9:
                    k = c = q.xml.Parser.doParse(a, c, e);
                    f = 1;
                    break;
                case 11:
                    switch (l) {
                        case 62:
                            f = 1;
                            break;
                        default:
                            throw "Expected >";
                    }
                    break;
                case 12:
                    switch (l) {
                        case 62:
                            return 0 == p && b.addChild(h.createPCData("")), c;
                        default:
                            throw "Expected >";
                    }
                case 10:
                    if (!(97 <= l && 122 >= l || 65 <= l && 90 >= l || 48 <= l && 57 >= l || 58 == l || 46 == l || 95 == l || 45 == l)) {
                        if (k == c) throw "Expected node name";
                        if (m.substr(a, k, c - k) != b.get_nodeName()) throw "Expected </" + b.get_nodeName() + ">";
                        f = 0;
                        d = 12;
                        continue
                    }
                    break;
                case 15:
                    45 == l && 45 == a.charCodeAt(c + 1) && 62 == a.charCodeAt(c + 2) && (b.addChild(h.createComment(m.substr(a, k, c - k))), c += 2, f = 1);
                    break;
                case 16:
                    91 == l ? n++ : 93 == l ? n-- : 62 == l && 0 == n && (b.addChild(h.createDocType(m.substr(a, k, c - k))), f = 1);
                    break;
                case 14:
                    63 == l && 62 == a.charCodeAt(c + 1) && (c++, f = m.substr(a,
                        k + 1, c - k - 2), b.addChild(h.createProcessingInstruction(f)), f = 1);
                    break;
                case 18:
                    59 == l && (k = m.substr(a, k, c - k), 35 == k.charCodeAt(0) ? (k = 120 == k.charCodeAt(1) ? y.parseInt("0" + m.substr(k, 1, k.length - 1)) : y.parseInt(m.substr(k, 1, k.length - 1)), s.add(String.fromCharCode(k))) : q.xml.Parser.escapes.exists(k) ? s.add(q.xml.Parser.escapes.get(k)) : s.b += y.string("&" + k + ";"), k = c + 1, f = d)
            }
            l = t.fastCodeAt(a, ++c)
        }
        1 == f && (k = c, f = 13);
        if (13 == f) return c == k && 0 != p || b.addChild(h.createPCData(s.b + m.substr(a, k, c - k))), c;
        throw "Unexpected end";
    };
    var u = {
        Boot: function() {}
    };
    u.Boot.__name__ = !0;
    u.Boot.getClass = function(a) {
        return a instanceof Array && null == a.__enum__ ? Array : a.__class__
    };
    u.Boot.__string_rec = function(a, c) {
        if (null == a) return "null";
        if (5 <= c.length) return "<...>";
        var b = typeof a;
        "function" == b && (a.__name__ || a.__ename__) && (b = "object");
        switch (b) {
            case "object":
                if (a instanceof Array) {
                    if (a.__enum__) {
                        if (2 == a.length) return a[0];
                        b = a[0] + "(";
                        c += "\t";
                        for (var e = 2, d = a.length; e < d;) var g = e++,
                            b = 2 != g ? b + ("," + u.Boot.__string_rec(a[g], c)) : b + u.Boot.__string_rec(a[g],
                                c);
                        return b + ")"
                    }
                    b = a.length;
                    e = "[";
                    c += "\t";
                    for (d = 0; d < b;) g = d++, e += (0 < g ? "," : "") + u.Boot.__string_rec(a[g], c);
                    return e + "]"
                }
                try {
                    e = a.toString
                } catch (h) {
                    return "???"
                }
                if (null != e && e != Object.toString && (b = a.toString(), "[object Object]" != b)) return b;
                b = null;
                e = "{\n";
                c += "\t";
                d = null != a.hasOwnProperty;
                for (b in a) d && !a.hasOwnProperty(b) || "prototype" == b || "__class__" == b || "__super__" == b || "__interfaces__" == b || "__properties__" == b || (2 != e.length && (e += ", \n"), e += c + b + " : " + u.Boot.__string_rec(a[b], c));
                c = c.substring(1);
                return e +
                    ("\n" + c + "}");
            case "function":
                return "<function>";
            case "string":
                return a;
            default:
                return String(a)
        }
    };
    u.Boot.__interfLoop = function(a, c) {
        if (null == a) return !1;
        if (a == c) return !0;
        var b = a.__interfaces__;
        if (null != b)
            for (var e = 0, d = b.length; e < d;) {
                var g = e++,
                    g = b[g];
                if (g == c || u.Boot.__interfLoop(g, c)) return !0
            }
        return u.Boot.__interfLoop(a.__super__, c)
    };
    u.Boot.__instanceof = function(a, c) {
        if (null == c) return !1;
        switch (c) {
            case H:
                return (a | 0) === a;
            case E:
                return "number" == typeof a;
            case F:
                return "boolean" == typeof a;
            case String:
                return "string" ==
                    typeof a;
            case Array:
                return a instanceof Array && null == a.__enum__;
            case I:
                return !0;
            default:
                if (null != a) {
                    if ("function" == typeof c && (a instanceof c || u.Boot.__interfLoop(u.Boot.getClass(a), c))) return !0
                } else return !1;
                return c == J && null != a.__name__ || c == K && null != a.__ename__ ? !0 : a.__enum__ == c
        }
    };
    u.Browser = function() {};
    u.Browser.__name__ = !0;
    u.Browser.createXMLHttpRequest = function() {
        if ("undefined" != typeof XMLHttpRequest) return new XMLHttpRequest;
        if ("undefined" != typeof ActiveXObject) return new ActiveXObject("Microsoft.XMLHTTP");
        throw "Unable to create XMLHttpRequest object.";
    };
    var d = {};
    d.Muses = n.muses.Muses = function(a) {
        this.src = this.name = this.lastMessage = null;
        this.progress = 0;
        this.lastAudioName = null;
        this.playURL = "";
        this.playTimeout = this.bufferingTimeout = 0;
        this.desiredStatus = "stop";
        this.audio = this.lastAudioStatus = this.lastAudioSrc = null;
        this.src = a.url;
        this.name = a.title;
        this.audio = new Audio;
        this.ui = new d.UI(this, a);
        a.autoplay && (a = window.navigator.userAgent.toLowerCase(), -1 == a.indexOf("iphone") && -1 == a.indexOf("ipad") && -1 == a.indexOf("ipod") &&
        this.playAudio())
        n.MRP.html = this;
    };
    d.Muses.__name__ = !0;
    d.Muses.initTimer = function(a) {
        -1 == m.indexOf(d.Muses.instances, a, 0) && d.Muses.instances.push(a);
        null == d.Muses.statusTimer && (d.Muses.statusTimer = new q.Timer(500), d.Muses.statusTimer.run = function() {
            for (var a = 0, b = d.Muses.instances; a < b.length;) {
                var e = b[a];
                ++a;
                try {
                    e.checkAudioStatus()
                } catch (f) {
                    if (u.Boot.__instanceof(f, String)) console.log("Error: " + f);
                    else throw f;
                }
            }
        })
    };
    d.Muses.prototype = {
        audio: null,
        lastAudioStatus: null,
        lastAudioSrc: null,
        desiredStatus: null,
        playTimeout: null,
        bufferingTimeout: null,
        playURL: null,
        lastAudioName: null,
        progress: null,
        src: null,
        name: null,
        lastMessage: null,
        ui: null,
        playAudio: function() {
            d.Muses.initTimer(this);
            this.stopAudio(!1);
            this.playURL = this.src;
            this.desiredStatus = "play";
            this.playTimeout = 3600;
            this.bufferingTimeout = 40;
            this.lastAudioSrc = this.audio.src = this.src;
            this.lastAudioName = this.name;
            this.lastAudioStatus = null;
            this.audio.autoplay = !0;
            this.audio.play();
            this.ui.setPlaying();
            d.Tracker.track(this.src, this.name, this.ui, !0)
        },
        stopAudio: function(a) {
            this.desiredStatus =
                "stop";
            null != this.audio && (this.audio.pause(), this.audio.src = "");
            a && (this.lastAudioStatus = 4)
        },
        retryAudio: function() {
            var a = this;
            this.lastAudioStatus = -1;
            q.Timer.delay(function() {
                -1 == a.lastAudioStatus && a.playAudio()
            }, 2E3)
        },
        setVolume: function(a) {
            this.audio.volume = a;
            null != this.ui && this.ui.setVolume(a)
        },
        checkAudioStatus: function() {
            var a = "",
                a = null;
            if (null != this.audio) {
                a = this.audio.networkState;
                y.string(this.audio.error);
                if (2 == a || 1 == a) a = 0 == this.audio.played.length ? 1 : 2;
                if (null != this.audio.error || 4 == this.lastAudioStatus) a =
                    3
            }
            0 == a ? (a = "Error al conectar", this.lastMessage != a && this.ui.setError()) : -1 == a ? a = "retry..." : null == a ? a = "init" : 1 == a ? (this.bufferingTimeout--, 0 == this.bufferingTimeout && this.retryAudio(), a = "Buffering... " + Math.round(this.bufferingTimeout / 2), this.lastMessage != a && this.ui.setBuffering()) : 2 == a ? (this.playTimeout--, 0 == this.playTimeout && this.retryAudio(), a = "Playing... ", this.lastMessage != a && this.ui.setPlaying()) : 4 == a || 3 == a ? "play" == this.desiredStatus ? (a = "Error de red", this.retryAudio(), this.lastMessage != a && this.ui.setError()) :
                (a = "Stopped.", this.lastMessage != a && this.ui.setStopped()) : (a = "ERROR: " + a, console.log(a));
            this.lastMessage = a
        },
        __class__: d.Muses
    };
    d.Tracker = function() {};
    d.Tracker.__name__ = !0;
    d.Tracker.track = function(a, c, g, e) {
        d.Tracker.enabled && (null == d.Tracker.tracked && (d.Tracker.tracked = new q.ds.StringMap, b.Stats.init("UA-12297597-1", "hosted.musesradioplayer.com")), e && d.Tracker.tracked.get(a) || (b.Stats.trackPageview("/tracker/track.php?version=0.2 beta&url=" + a + "&player=HTML5&skin=" + g.skin, "Muses - HTML5 Tracking [Radio: " +
        c + "]"), d.Tracker.tracked.set(a, !0)))
    };
    d.UI = n.muses.UI = function(a, c) {
        this.skinFolder = this.baseURL = this.skinDomain = "";
        this.togglePlayStopEnabled = this.lastToggleValue = !1;
        this.mainDiv = this.playButton = this.stopButton = this.volumeControl = this.bg = this.statusText = this.artistText = this.songTitleText = this.statusLed = null;
        this.skin = "";
        var b = this;
        this.title = c.title;
        this.skin = c.skin;
        this.muses = a;
        this.mainDiv = window.document.getElementById(c.elementId);
        this.mainDiv.style.position = "relative";
        this.statusText = new d.skin.TitleText(this);
        this.artistText = new d.skin.TitleText(this);
        this.songTitleText = new d.skin.TitleText(this);
        this.statusLed = new d.skin.StatusLed(this);
        this.volumeControl = new d.skin.VolumeControl(this, this.muses);
        this.volumeControl.setVolume(c.volume / 100);
        this.playButton = new d.skin.Button(this, "play");
        this.stopButton = new d.skin.Button(this, "stop");
        this.loadSkin(this.skin);
        this.statusLed.configured && this.mainDiv.appendChild(this.statusLed.container);
        this.statusText.configured && this.mainDiv.appendChild(this.statusText.container);
        this.artistText.configured && this.mainDiv.appendChild(this.artistText.container);
        this.songTitleText.configured && this.mainDiv.appendChild(this.songTitleText.container);
        this.volumeControl.configured && this.mainDiv.appendChild(this.volumeControl.container);
        this.mainDiv.appendChild(this.playButton.container);
        this.mainDiv.appendChild(this.stopButton.container);
        this.stopButton.container.onclick = function(a) {
            b.muses.stopAudio(!1)
        };
        this.playButton.container.onclick = function(a) {
            b.muses.playAudio()
        };
        this.showInfo(c.welcome)
    };
    d.UI.__name__ = !0;
    d.UI.parseInt = function(a, c) {
        return null == a ? c : y.parseInt(a)
    };
    d.UI.prototype = {
        skin: null,
        mainDiv: null,
        playButton: null,
        stopButton: null,
        volumeControl: null,
        bg: null,
        statusText: null,
        artistText: null,
        songTitleText: null,
        statusLed: null,
        togglePlayStopEnabled: null,
        lastToggleValue: null,
        skinFolder: null,
        baseURL: null,
        skinDomain: null,
        title: null,
        titleTimer: null,
        muses: null,
        XmlToLower: function(a) {
            for (var c = a.attributes(); c.hasNext();) {
                var b = c.next();
                a.set(b.toLowerCase(), a.get(b))
            }
        },
        enablePlayStopToggle: function() {
            this.togglePlayStopEnabled = !0;
            this.togglePlayStop(this.lastToggleValue)
        },
        togglePlayStop: function(a) {
            this.lastToggleValue = a;
            this.togglePlayStopEnabled && (this.playButton.setVisible(!a), this.stopButton.setVisible(a))
        },
        makeAbsolute: function(a) {
            return -1 != a.indexOf("://") ? a : "/" == a.charAt(0) ? this.skinDomain + a : this.baseURL + a
        },
        getDomainName: function(a) {
            a += "/";
            var c = a.indexOf("://");
            if (-1 == c) return "";
            c = a.indexOf("/", c + 3);
            return m.substr(a, 0, c)
        },
        getDirName: function(a) {
            var c = a.lastIndexOf("/");
            return -1 == c ? "" : m.substr(a, 0, c + 1)
        },
        loadSkin: function(a) {
            var c =
                q.Http.requestUrl(a);
            this.baseURL = this.getDirName(a);
            this.skinDomain = this.getDomainName(a);
            a = !1;
            for (c = h.parse(c).elements(); c.hasNext();) {
                var b = c.next();
                if ("ffmp3-skin" != b.get_nodeName().toLowerCase() && "muses-skin" != b.get_nodeName().toLowerCase()) break;
                this.XmlToLower(b);
                null == b.get("folder") ? this.skinFolder = "" : this.skinFolder = b.get("folder");
                (a = null == b.get("toggleplaystop") ? !1 : "true" == b.get("toggleplaystop")) && this.enablePlayStopToggle();
                0 < this.skinFolder.length && "/" != this.skinFolder.charAt(this.skinFolder.length -
                1) && (this.skinFolder += "/");
                this.skinFolder = this.makeAbsolute(this.skinFolder);
                for (a = b.elements(); a.hasNext();) switch (b = a.next(), this.XmlToLower(b), b.get_nodeName().toLowerCase()) {
                    case "bg":
                        this.configureBG(b);
                        break;
                    case "play":
                        this.playButton.configure(b);
                        break;
                    case "stop":
                        this.stopButton.configure(b);
                        break;
                    case "text":
                        this.statusText.configureText(b, "left");
                        break;
                    case "status":
                        this.statusLed.configure(b);
                        break;
                    case "volume":
                        this.volumeControl.configure(b);
                        break;
                    case "artist":
                        this.artistText.configureText(b,
                            "left");
                        break;
                    case "songtitle":
                        this.songTitleText.configureText(b, "left")
                }
            }
        },
        loadImage: function(a, c) {
            a.src = this.skinFolder + c
        },
        configureBG: function(a) {
            this.bg = new Image;
            this.loadImage(this.bg, a.get("image"));
            this.bg.style.position = "absolute";
            this.bg.style.left = d.UI.parseInt(a.get("x"), 0) + "px";
            this.bg.style.top = d.UI.parseInt(a.get("y"), 0) + "px";
            this.mainDiv.appendChild(this.bg)
        },
        configureButton: function(a, c) {
            a.src = this.skinFolder + c.get("image");
            a.style.position = "absolute";
            a.style.left = d.UI.parseInt(c.get("x"),
                0) + "px";
            a.style.top = d.UI.parseInt(c.get("y"), 0) + "px"
        },
        setPlaying: function() {
            this.showInfo("Play");
            this.statusLed.on();
            this.togglePlayStop(!0)
        },
        setStopped: function() {
            this.showInfo("Stop");
            this.statusLed.off();
            this.togglePlayStop(!1)
        },
        setBuffering: function() {
            this.showInfo("Buffering");
            this.statusLed.on();
            this.togglePlayStop(!0)
        },
        setError: function() {
            this.showInfo("Error");
            this.statusLed.off()
        },
        setVolume: function(a) {
            this.volumeControl.setVolume(a);
            this.showInfo("Volume: " + Math.round(100 * a) + "%")
        },
        showInfo: function(a,
                           c) {
            null == c && (c = !0);
            null == a ? this.restoreTitle() : (null != this.titleTimer && this.titleTimer.stop(), this.statusText.setText(a), c && (this.titleTimer = new q.Timer(2E3), this.titleTimer.run = p(this, this.restoreTitle)))
        },
        restoreTitle: function() {
            null != this.titleTimer && this.titleTimer.stop();
            this.statusText.setText(this.title)
        },
        __class__: d.UI
    };
    d.skin = {};
    d.skin.UIComponent = function(a) {
        this.ui = a;
        this.configured = !1;
        this.container = window.document.createElement("div");
        this.container.style.position = "absolute"
    };
    d.skin.UIComponent.__name__ = !0;
    d.skin.UIComponent.prototype = {
        container: null,
        configured: null,
        ui: null,
        setVisible: function(a) {
            this.container.style.display = a ? "block" : "none"
        },
        configure: function(a) {
            this.configured = !0;
            this.container.style.left = d.UI.parseInt(a.get("x"), 0) + "px";
            this.container.style.top = d.UI.parseInt(a.get("y"), 0) + "px";
            null != a.get("width") && (this.container.style.width = d.UI.parseInt(a.get("width"), 0) + "px");
            null != a.get("height") && (this.container.style.height = d.UI.parseInt(a.get("height"), 0) + "px")
        },
        appendChild: function(a,
                              c) {
            null == c && (c = !0);
            a.style.position = "absolute";
            a.style.left = a.style.top = "0px";
            a.style.display = c ? "block" : "none";
            this.container.appendChild(a)
        },
        __class__: d.skin.UIComponent
    };
    d.skin.Button = function(a, c) {
        var b = this;
        d.skin.UIComponent.call(this, a);
        this.mouseOverState = new Image;
        this.mouseDownState = new Image;
        this.noMouseState = new Image;
        this.container.title = c;
        this.mouseDownState.style.opacity = "0";
        this.mouseOverState.style.opacity = "0";
        this.container.onmouseup = function(a) {
            b.mouseDownState.style.opacity = "0";
            b.mouseOverState.style.opacity = "1"
        };
        this.container.onmousedown = function(a) {
            b.mouseDownState.style.opacity = "1";
            b.mouseOverState.style.opacity = "0"
        };
        this.container.onmouseover = function(a) {
            b.mouseOverState.style.opacity = "1"
        };
        this.container.onmouseout = function(a) {
            b.mouseDownState.style.opacity = "0";
            b.mouseOverState.style.opacity = "0"
        }
    };
    d.skin.Button.__name__ = !0;
    d.skin.Button.__super__ = d.skin.UIComponent;
    d.skin.Button.prototype = w(d.skin.UIComponent.prototype, {
        mouseOverState: null,
        mouseDownState: null,
        noMouseState: null,
        configure: function(a) {
            d.skin.UIComponent.prototype.configure.call(this, a);
            null != a.get("bgimage") && (this.ui.loadImage(this.noMouseState, a.get("bgimage")), this.appendChild(this.noMouseState));
            null != a.get("clickimage") && (this.ui.loadImage(this.mouseDownState, a.get("clickimage")), this.appendChild(this.mouseDownState));
            this.ui.loadImage(this.mouseOverState, a.get("image"));
            this.appendChild(this.mouseOverState)
        },
        __class__: d.skin.Button
    });
    d.skin.StatusLed = function(a) {
        d.skin.UIComponent.call(this, a);
        this.playMC =
            new Image;
        this.stopMC = new Image
    };
    d.skin.StatusLed.__name__ = !0;
    d.skin.StatusLed.__super__ = d.skin.UIComponent;
    d.skin.StatusLed.prototype = w(d.skin.UIComponent.prototype, {
        playMC: null,
        stopMC: null,
        configure: function(a) {
            d.skin.UIComponent.prototype.configure.call(this, a);
            null != a.get("imageplay") && -1 == a.get("imageplay").indexOf(".swf") && (this.ui.loadImage(this.playMC, a.get("imageplay")), this.appendChild(this.playMC, !1));
            null != a.get("imagestop") && -1 == a.get("imagestop").indexOf(".swf") && (this.ui.loadImage(this.stopMC,
                a.get("imagestop")), this.appendChild(this.stopMC, !0))
        },
        on: function() {
            this.playMC.style.display = "block";
            this.stopMC.style.display = "none"
        },
        off: function() {
            this.playMC.style.display = "none";
            this.stopMC.style.display = "block"
        },
        __class__: d.skin.StatusLed
    });
    d.skin.TitleText = function(a) {
        d.skin.UIComponent.call(this, a);
        this.container.style.fontFamily = "Silkscreen";
        this.container.style.fontSize = "12px"
    };
    d.skin.TitleText.__name__ = !0;
    d.skin.TitleText.__super__ = d.skin.UIComponent;
    d.skin.TitleText.prototype = w(d.skin.UIComponent.prototype, {
        configureText: function(a, b) {
            this.configure(a);
            switch (a.get("align")) {
                case "center":
                    this.container.style.textAlign = "center";
                    break;
                case "right":
                    this.container.style.textAlign = "right";
                    break;
                default:
                    this.container.style.textAlign = b
            }
            this.container.style.padding = "2px";
            this.container.style.whiteSpace = "nowrap";
            this.container.style.fontFamily = a.get("font");
            this.container.style.fontSize = d.UI.parseInt(a.get("size"), 12) + "px";
            this.container.style.color = a.get("color");
            this.container.style.overflow = "hidden"
        },
        setText: function(a) {
            this.container.innerHTML =
                a
        },
        __class__: d.skin.TitleText
    });
    d.skin.VolumeControl = function(a, b) {
        d.skin.UIComponent.call(this, a);
        this.muses = b;
        this.firstDraw = !0;
        this.bars = null;
        this.mousePressed = !1;
        this.volume = 1;
        this.setMode("bars");
        this.draw(this.container);
        this.vertMargin = this.horizMargin = this.height = this.width = 0;
        this.barStep = 2;
        this.barWidth = 1;
        this.barColors = this.bgColors = null
    };
    d.skin.VolumeControl.__name__ = !0;
    d.skin.VolumeControl.__super__ = d.skin.UIComponent;
    d.skin.VolumeControl.prototype = w(d.skin.UIComponent.prototype, {
        volume: null,
        width: null,
        height: null,
        horizMargin: null,
        horizDesp: null,
        vertMargin: null,
        vertDesp: null,
        barStep: null,
        barWidth: null,
        bgColors: null,
        barColors: null,
        bars: null,
        cover: null,
        spriteBar: null,
        firstDraw: null,
        mode: null,
        holder: null,
        mousePressed: null,
        muses: null,
        draw: function(a) {},
        setMode: function(a) {
            switch (a.toLowerCase()) {
                case "bars":
                    this.draw = p(this, this.drawBars);
                    break;
                case "holder":
                    this.draw = p(this, this.drawHolder);
                    break;
                case "vholder":
                    this.draw = p(this, this.drawVHolder)
            }
            this.mode = a
        },
        drawHolder: function(a) {
            this.holder.style.left =
                this.volume * (this.width - this.holder.width) + "px"
        },
        drawVHolder: function(a) {
            this.holder.style.top = (1 - this.volume) * (this.height - this.holder.height) + "px"
        },
        drawBars: function(a) {
            if (null != this.barColors && 0 != this.barStep && (a = Math.round((this.width - 2 * this.horizMargin) / this.barStep), 0 != a)) {
                var b = (this.height - 2 * this.vertMargin + 1) / a,
                    d = this.height - this.vertMargin,
                    e = this.horizMargin;
                if (null == this.bars) {
                    this.bars = [];
                    for (var f = 0; f < a;) {
                        var g = f++,
                            h;
                        h = window.document.createElement("div");
                        this.bars.push(h);
                        this.appendChild(h);
                        h.style.left = e + g * this.barStep + "px";
                        h.style.top = d - g * b + "px";
                        h.style.width = Math.round(this.barWidth) + "px";
                        h.style.height = Math.ceil(g * b) + "px"
                    }
                }
                b = 0;
                for (d = Math.round(this.volume * a); b < d;) e = b++, this.bars[e].style.backgroundColor = this.barColors[0];
                for (b = Math.round(this.volume * a); b < a;) d = b++, this.bars[d].style.backgroundColor = this.barColors[1]
            }
        },
        setVolume: function(a) {
            this.volume != a && (this.volume = a, 1 < this.volume && (this.volume = 1), 0 > this.volume && (this.volume = 0), this.muses.setVolume(this.volume), this.draw(this.container))
        },
        getVolume: function() {
            return this.volume
        },
        mouseDown: function(a) {
            var b;
            this.mousePressed = !0;
            "vholder" != this.mode ? (a = a.layerX, b = this.width) : (a = this.height - a.layerY, b = this.height);
            a -= .06 * b;
            0 > a && (a = 0);
            a = Math.round(1.06 * a);
            a > b && (a = b);
            this.setVolume(a / (b - 2))
        },
        mouseUp: function(a) {
            this.mousePressed = !1
        },
        mouseMove: function(a) {
            this.mousePressed && this.mouseDown(a)
        },
        mouseWheel: function(a) {
            0 < a.wheelDelta ? this.setVolume(this.volume + .025) : this.setVolume(this.volume - .025)
        },
        configure: function(a) {
            d.skin.UIComponent.prototype.configure.call(this,
                a);
            this.width = d.UI.parseInt(a.get("width"), 0);
            this.height = d.UI.parseInt(a.get("height"), 0);
            this.barColors = [a.get("color1"), a.get("color2")];
            this.barStep = d.UI.parseInt(a.get("barstep"), 2);
            this.barWidth = d.UI.parseInt(a.get("barwidth"), 1);
            var b;
            b = null != a.get("mode") ? a.get("mode").toLowerCase() : null;
            this.setMode(b);
            if ("holder" == b || "vholder" == b) this.holder = new Image, this.holder.onload = p(this, this.holderLoad), this.ui.loadImage(this.holder, a.get("holderimage")), this.appendChild(this.holder);
            this.draw(this.container);
            this.cover = window.document.createElement("div");
            this.cover.onmousedown = p(this, this.mouseDown);
            this.cover.onmousemove = p(this, this.mouseMove);
            this.cover.onmousewheel = p(this, this.mouseWheel);
            this.cover.onmouseup = p(this, this.mouseUp);
            this.cover.onmouseout = p(this, this.mouseUp);
            this.cover.style.width = this.container.style.width;
            this.cover.style.height = this.container.style.height;
            this.appendChild(this.cover)
        },
        holderLoad: function(a) {
            this.holder.style.left = .5 * (this.width - this.holder.width) + "px";
            this.holder.style.top =
                .5 * (this.height - this.holder.height) + "px";
            this.draw(this.container)
        },
        __class__: d.skin.VolumeControl
    });
    var G = 0;
    Array.prototype.indexOf && (m.indexOf = function(a, b, d) {
        return Array.prototype.indexOf.call(a, b, d)
    });
    Math.NaN = Number.NaN;
    Math.NEGATIVE_INFINITY = Number.NEGATIVE_INFINITY;
    Math.POSITIVE_INFINITY = Number.POSITIVE_INFINITY;
    Math.isFinite = function(a) {
        return isFinite(a)
    };
    Math.isNaN = function(a) {
        return isNaN(a)
    };
    String.prototype.__class__ = String;
    String.__name__ = !0;
    Array.__name__ = !0;
    Date.prototype.__class__ =
        Date;
    Date.__name__ = ["Date"];
    var H = {
            __name__: ["Int"]
        },
        I = {
            __name__: ["Dynamic"]
        },
        E = Number;
    E.__name__ = ["Float"];
    var F = Boolean;
    F.__ename__ = ["Bool"];
    var J = {
            __name__: ["Class"]
        },
        K = {};
    h.Element = "element";
    h.PCData = "pcdata";
    h.CData = "cdata";
    h.Comment = "comment";
    h.DocType = "doctype";
    h.ProcessingInstruction = "processingInstruction";
    h.Document = "document";
    g.objectId = "MRPObject";
    g.playerCounter = 0;
    g.__hostPrefix = "hosted";
    g.__hostMidfix = "muses";
    b.Campaign.TYPE_DIRECT = "direct";
    b.Campaign.TYPE_ORGANIC = "organic";
    b.Campaign.TYPE_REFERRAL =
        "referral";
    b.Config.ERROR_SEVERITY_SILENCE = 0;
    b.Config.ERROR_SEVERITY_TRACE = 1;
    b.Config.ERROR_SEVERITY_EXCEPTIONS = 2;
    b.CustomVariable.SCOPE_VISITOR = 1;
    b.CustomVariable.SCOPE_SESSION = 2;
    b.CustomVariable.SCOPE_PAGE = 3;
    b.Page.REFERRER_INTERNAL = "0";
    b.Tracker.VERSION = "5.2.5";
    b.URLParser.parts = "source protocol authority userInfo user password host port relative path directory file query anchor".split(" ");
    b.internals.X10.OBJECT_KEY_NUM = 1;
    b.internals.X10.TYPE_KEY_NUM = 2;
    b.internals.X10.LABEL_KEY_NUM = 3;
    b.internals.X10.VALUE_VALUE_NUM =
        1;
    b.internals.request.Request.TYPE_EVENT = "event";
    b.internals.request.Request.TYPE_TRANSACTION = "tran";
    b.internals.request.Request.TYPE_ITEM = "item";
    b.internals.request.Request.TYPE_SOCIAL = "social";
    b.internals.request.Request.TYPE_CUSTOMVARIABLE = "var";
    b.internals.request.Request.X10_CUSTOMVAR_NAME_PROJECT_ID = "8";
    b.internals.request.Request.X10_CUSTOMVAR_VALUE_PROJECT_ID = "9";
    b.internals.request.Request.X10_CUSTOMVAR_SCOPE_PROJECT_ID = "11";
    b.internals.request.Request.CAMPAIGN_DELIMITER = "|";
    b.internals.request.EventRequest.X10_EVENT_PROJECT_ID =
        "5";
    q.xml.Parser.escapes = function(a) {
        a = new q.ds.StringMap;
        a.set("lt", "<");
        a.set("gt", ">");
        a.set("amp", "&");
        a.set("quot", '"');
        a.set("apos", "'");
        a.set("nbsp", String.fromCharCode(160));
        return a
    }(this);
    d.Muses.VERSION = "0.2 beta";
    d.Muses.instances = [];
    d.Tracker.enabled = !0;
    g.main()
})("undefined" != typeof window ? window : exports);
var FlashDetect = new function() {
    var n = this;
    n.installed = !1;
    n.raw = "";
    n.major = -1;
    n.minor = -1;
    n.revision = -1;
    n.revisionStr = "";
    var w = [{
            name: "ShockwaveFlash.ShockwaveFlash.7",
            version: function(p) {
                return B(p)
            }
        }, {
            name: "ShockwaveFlash.ShockwaveFlash.6",
            version: function(p) {
                var n = "6,0,21";
                try {
                    p.AllowScriptAccess = "always", n = B(p)
                } catch (m) {}
                return n
            }
        }, {
            name: "ShockwaveFlash.ShockwaveFlash",
            version: function(p) {
                return B(p)
            }
        }],
        B = function(p) {
            var n = -1;
            try {
                n = p.GetVariable("$version")
            } catch (m) {}
            return n
        };
    n.majorAtLeast = function(p) {
        return n.major >=
            p
    };
    n.minorAtLeast = function(p) {
        return n.minor >= p
    };
    n.revisionAtLeast = function(p) {
        return n.revision >= p
    };
    n.versionAtLeast = function(p) {
        var s = [n.major, n.minor, n.revision],
            m = Math.min(s.length, arguments.length);
        for (i = 0; i < m; i++)
            if (s[i] >= arguments[i]) {
                if (!(i + 1 < m && s[i] == arguments[i])) return !0
            } else return !1
    };
    n.FlashDetect = function() {
        var p, s, m, x, A;
        if (navigator.plugins && 0 < navigator.plugins.length) {
            var g = navigator.mimeTypes;
            if (g && g["application/x-shockwave-flash"] && g["application/x-shockwave-flash"].enabledPlugin &&
                g["application/x-shockwave-flash"].enabledPlugin.description) {
                p = g = g["application/x-shockwave-flash"].enabledPlugin.description;
                var g = p.split(/ +/),
                    z = g[2].split(/\./),
                    g = g[3];
                s = parseInt(z[0], 10);
                m = parseInt(z[1], 10);
                x = g;
                A = parseInt(g.replace(/[a-zA-Z]/g, ""), 10) || n.revision;
                n.raw = p;
                n.major = s;
                n.minor = m;
                n.revisionStr = x;
                n.revision = A;
                n.installed = !0
            }
        } else if (-1 == navigator.appVersion.indexOf("Mac") && window.execScript)
            for (g = -1, z = 0; z < w.length && -1 == g; z++) {
                p = -1;
                try {
                    p = new ActiveXObject(w[z].name)
                } catch (v) {
                    p = {
                        activeXError: !0
                    }
                }
                p.activeXError ||
                (n.installed = !0, g = w[z].version(p), -1 != g && (p = g, x = p.split(","), s = parseInt(x[0].split(" ")[1], 10), m = parseInt(x[1], 10), A = parseInt(x[2], 10), x = x[2], n.raw = p, n.major = s, n.minor = m, n.revision = A, n.revisionStr = x))
            }
    }()
};
FlashDetect.JS_RELEASE = "1.0.4";