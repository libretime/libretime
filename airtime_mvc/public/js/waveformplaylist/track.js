'use strict';

var TrackEditor = function() {

};

TrackEditor.prototype.states = {
    select: {
        events: {
            mousedown: "selectStart"
        },

        classes: [
            "state-select"
        ]
    },
    
    shift: {
        events: {
            mousedown: "timeShift"
        },

        classes: [
            "state-shift"
        ]
    }
};

TrackEditor.prototype.setConfig = function(config) {
    this.config = config;
};

TrackEditor.prototype.setWidth = function(width) {
    this.width = width;
};

TrackEditor.prototype.init = function(src, start, end, fades, cues) {
   
    makePublisher(this);

    this.container = document.createElement("div");

    this.drawer = new WaveformDrawer();
    this.drawer.init(this.container, this.config);

    this.playout = new AudioPlayout();
    this.playout.init(this.config);

    this.sampleRate = this.config.getSampleRate();
    this.resolution = this.config.getResolution();

    //value is a float in seconds
    this.startTime = start || 0;
    //value is a float in seconds
    this.endTime = end || 0; //set properly in onTrackLoad.

    this.leftOffset = this.secondsToSamples(this.startTime); //value is measured in samples.

    this.prevStateEvents = {};
    this.setState(this.config.getState());

    this.fades = fades || {};

    if (cues.cuein !== undefined) {
        this.setCuePoints(this.secondsToSamples(cues.cuein), this.secondsToSamples(cues.cueout));
    }
    
    this.selectedArea = undefined; //selected area of track stored as inclusive buffer indices to the audio buffer.
    this.active = false;

    this.container.classList.add("channel-wrapper");
    this.container.style.left = this.leftOffset;

    this.drawer.drawLoading();

    return this.container;
};

TrackEditor.prototype.getFadeId = function() {
    var id = ""+Math.random();

    return id.replace(".", "");
};

TrackEditor.prototype.getBuffer = function() {
    return this.playout.getBuffer();
};

TrackEditor.prototype.setBuffer = function(buffer) {
    this.playout.setBuffer(buffer);
};


TrackEditor.prototype.loadTrack = function(track) {
    var el;

    el = this.init(
        track.src, 
        track.start, 
        track.end, 
        track.fades,
        {
            cuein: track.cuein,
            cueout: track.cueout
        }
    );
    this.loadBuffer(track.src);

    return el;
};

/**
 * Loads an audio file via XHR.
 */
TrackEditor.prototype.loadBuffer = function(src) {
    var that = this,
        xhr = new XMLHttpRequest();

    xhr.responseType = 'arraybuffer';

    xhr.addEventListener('progress', function(e) {
        var percentComplete;

        if (e.lengthComputable) {
            percentComplete = e.loaded / e.total * 100;
            that.drawer.updateLoader(percentComplete);
        } 

    }, false);

    xhr.addEventListener('load', function(e) {
        that.src = src;
        that.drawer.setLoaderState("decoding");

        that.playout.loadData(
            e.target.response,
            that.onTrackLoad.bind(that)
        );
    }, false);

    xhr.open('GET', src, true);
    xhr.send();
};

TrackEditor.prototype.drawTrack = function(buffer) {

    this.drawer.drawBuffer(buffer, this.getPixelOffset(this.leftOffset), this.cues);
    this.drawer.drawFades(this.fades);
};

TrackEditor.prototype.onTrackLoad = function(buffer) {
    var res;

    if (this.cues === undefined) {
        this.setCuePoints(0, buffer.length - 1);
    }

    if (this.width !== undefined) {
        res = Math.ceil(buffer.length / this.width);

        this.config.setResolution(res);
        this.resolution = res;
    }
   
    this.drawTrack(buffer);
};

TrackEditor.prototype.samplesToSeconds = function(samples) {
    return samples / this.sampleRate;
};

TrackEditor.prototype.secondsToSamples = function(seconds) {
    return Math.ceil(seconds * this.sampleRate);
};

TrackEditor.prototype.samplesToPixels = function(samples) {
    return ~~(samples / this.resolution);
};

TrackEditor.prototype.pixelsToSamples = function(pixels) {
    return ~~(pixels * this.resolution);
};

TrackEditor.prototype.pixelsToSeconds = function(pixels) {
    return pixels * this.resolution / this.sampleRate;
};

TrackEditor.prototype.secondsToPixels = function(seconds) {
    return ~~(seconds * this.sampleRate / this.resolution);
};

TrackEditor.prototype.getPixelOffset = function() {
    return this.leftOffset / this.resolution;
};

TrackEditor.prototype.activate = function() {
    this.active = true;
    this.container.classList.add("active");
};

TrackEditor.prototype.deactivate = function() {
    this.active = false;
    this.selectedArea = undefined;
    this.container.classList.remove("active");
    this.drawer.draw(-1, this.getPixelOffset());
};

/* start of state methods */

TrackEditor.prototype.timeShift = function(e) {
    var el = e.currentTarget, //want the events placed on the channel wrapper.
        startX = e.pageX, 
        diffX = 0, 
        origX = 0,
        updatedX = 0,
        editor = this,
        res = editor.resolution,
        scroll = this.config.getTrackScroll(),
        scrollX = scroll.left;

    origX = editor.leftOffset/res;
    
    //dynamically put an event on the element.
    el.onmousemove = function(e) {
        var endX = e.pageX;
        
        diffX = endX - startX;
        updatedX = origX + diffX;
        editor.drawer.setTimeShift(updatedX);
        editor.leftOffset = editor.pixelsToSamples(updatedX);
    };
    el.onmouseup = function() {
        var delta;

        el.onmousemove = document.body.onmouseup = null;
        editor.leftOffset = editor.pixelsToSamples(updatedX);
        delta = editor.pixelsToSeconds(diffX);

        //update track's start and end time relative to the playlist.
        editor.startTime = editor.startTime + delta;
        editor.endTime = editor.endTime + delta;
    };
};

/*
    startTime, endTime in seconds.
*/
TrackEditor.prototype.notifySelectUpdate = function(startTime, endTime) {
   
    this.fire('changecursor', {
        start: startTime,
        end: endTime,
        editor: this
    });
};


TrackEditor.prototype.getSelectedPlayTime = function() {
    var selected = this.selectedArea,
        offset = this.leftOffset,
        start = this.samplesToSeconds(offset + selected.start),
        end = this.samplesToSeconds(offset + selected.end);

    return {
        startTime: start,
        endTime: end
    }
};


TrackEditor.prototype.getSelectedArea = function() {
    return this.selectedArea;
};

/*
    start, end in samples.
*/
TrackEditor.prototype.adjustSelectedArea = function(start, end) {
    var buffer = this.getBuffer();

    if (start < 0) {
        start = 0;
    }

    if (end > buffer.length - 1) {
        end = buffer.length - 1;
    }

    return {
        start: start,
        end: end
    };
};

/*
    start, end in pixels
*/
TrackEditor.prototype.setSelectedArea = function(start, end, shiftKey) {
    var left, 
        right,
        currentStart,
        currentEnd;

    //extending selected area since shift is pressed.
    if (shiftKey && (end - start === 0) && (this.prevSelectedArea !== undefined)) {

        currentStart = this.samplesToPixels(this.prevSelectedArea.start);
        currentEnd = this.samplesToPixels(this.prevSelectedArea.end);

        if (start < currentStart) {
            left = start;
            right = currentEnd;
        }
        else if (end > currentEnd) {
            left = currentStart;
            right = end;
        }
        //it's ambigous otherwise, cut off the smaller duration.
        else {
            if ((start - currentStart) < (currentEnd - start)) {
                left = start;
                right = currentEnd;
            }
            else {
                left = currentStart;
                right = end;
            }
        }
    }
    else {
        left = start;
        right = end;
    }

    this.prevSelectedArea = this.selectedArea;
    this.selectedArea = this.adjustSelectedArea(this.pixelsToSamples(left), this.pixelsToSamples(right));
};

TrackEditor.prototype.activateAudioSelection = function() {

    this.fire("activateSelection");
};

TrackEditor.prototype.deactivateAudioSelection = function() {

    this.fire("deactivateSelection");
};

TrackEditor.prototype.selectStart = function(e) {
    var el = e.currentTarget, //want the events placed on the channel wrapper.
        editor = this,
        //scroll = this.config.getTrackScroll(),
        //scrollX = scroll.left,
        //startX = scrollX + (e.layerX || e.offsetX), //relative to e.target (want the canvas).
        //prevX = scrollX + (e.layerX || e.offsetX),
        startX = e.layerX || e.offsetX, //relative to e.target (want the canvas).
        prevX = e.layerX || e.offsetX,
        offset = this.leftOffset,
        startTime;

    if (e.target.tagName !== "CANVAS") {
        return;
    }

    editor.setSelectedArea(startX, startX);
    startTime = editor.samplesToSeconds(offset + editor.selectedArea.start);

    editor.updateEditor(-1, undefined, undefined, true);
    editor.notifySelectUpdate(startTime, startTime);

    //dynamically put an event on the element.
    el.onmousemove = function(e) {
        var currentX = e.layerX || e.offsetX,
            //currentX = scrollX + (e.layerX || e.offsetX),
            delta = currentX - prevX,
            minX = Math.min(prevX, currentX, startX),
            maxX = Math.max(prevX, currentX, startX),
            selectStart,
            selectEnd,
            startTime, endTime;
        
        if (currentX > startX) {
            selectStart = startX;
            selectEnd = currentX;
        }
        else {
            selectStart = currentX;
            selectEnd = startX;
        }

        startTime = editor.samplesToSeconds(offset + editor.selectedArea.start);
        endTime = editor.samplesToSeconds(offset + editor.selectedArea.end);

        editor.setSelectedArea(selectStart, selectEnd);
        editor.updateEditor(-1, undefined, undefined, true);
        editor.notifySelectUpdate(startTime, endTime);
        prevX = currentX;
    };
    el.onmouseup = function(e) {
        var endX = e.layerX || e.offsetX,
            //endX = scrollX + (e.layerX || e.offsetX),
            minX, maxX,
            cursorPos,
            startTime, endTime;

        minX = Math.min(startX, endX);
        maxX = Math.max(startX, endX);

        editor.setSelectedArea(minX, maxX, e.shiftKey);

        minX = editor.samplesToPixels(offset + editor.selectedArea.start);
        maxX = editor.samplesToPixels(offset + editor.selectedArea.end);

        el.onmousemove = document.body.onmouseup = null;
        
        //if more than one pixel is selected, listen to possible fade events.
        if (Math.abs(minX - maxX)) {
            editor.activateAudioSelection();
        }
        else {
            editor.deactivateAudioSelection();
        }

        cursorPos = startTime = editor.samplesToSeconds(offset + editor.selectedArea.start);
        endTime = editor.samplesToSeconds(offset + editor.selectedArea.end);

        editor.updateEditor(-1, undefined, undefined, true);
        editor.config.setCursorPos(cursorPos);
        editor.notifySelectUpdate(startTime, endTime);    
    };
};

/* end of state methods */

TrackEditor.prototype.saveFade = function(id, type, shape, start, end) {
    
    this.fades[id] = {
        type: type,
        shape: shape,
        start: start,
        end: end
    };

    return id;
};

TrackEditor.prototype.removeFade = function(id) {

    delete this.fades[id];
};

/*
    Cue points are stored internally in the editor as sample indices for highest precision.

    sample at index cueout is not included.
*/
TrackEditor.prototype.setCuePoints = function(cuein, cueout) {
    var offset = this.cues ? this.cues.cuein : 0;

    this.cues = {
        cuein: offset + cuein,
        cueout: offset + cueout
    };

    this.duration = (cueout - cuein) / this.sampleRate;
    this.endTime = this.duration + this.startTime;
};

/*
    Will remove all audio samples from the track's buffer except for the currently selected area.
    Used to set cuein / cueout points in the audio.

    start, end are indices into the audio buffer and are inclusive.
*/
TrackEditor.prototype.trim = function(start, end) {
    
    this.setCuePoints(start, end+1);
    this.resetCursor();
    this.drawTrack(this.getBuffer());
};


/*
    Will remove all audio samples from the track's buffer in the currently selected area.

    start, end are indices into the audio buffer and are inclusive.
*/
TrackEditor.prototype.removeAudio = function(start, end) {
    
};

TrackEditor.prototype.onTrackEdit = function(event) {
    var type = event.type,
        method = "on" + type.charAt(0).toUpperCase() + type.slice(1);

    if (this.active === true) {
        this[method].call(this, event.args);
    }
};

TrackEditor.prototype.onCreateFade = function(args) {
    var selected = this.selectedArea,
        pixelOffset = this.getPixelOffset(),
        start = this.samplesToPixels(selected.start),
        end = this.samplesToPixels(selected.end),
        startTime = this.samplesToSeconds(selected.start),
        endTime = this.samplesToSeconds(selected.end),
        id = this.getFadeId();

    this.resetCursor();
    this.saveFade(id, args.type, args.shape, startTime, endTime);
    this.drawer.draw(-1, pixelOffset);
    this.drawer.drawFade(id, args.type, args.shape, start, end);

    this.deactivateAudioSelection();
};

TrackEditor.prototype.onZeroCrossing = function() {
    var selected = this.getSelectedArea(),
        startTime,
        endTime,
        offset = this.leftOffset;

    this.selectedArea = this.findNearestZeroCrossing(selected.start, selected.end);

    startTime = this.samplesToSeconds(offset + this.selectedArea.start);
    endTime = this.samplesToSeconds(offset + this.selectedArea.end);
    this.notifySelectUpdate(startTime, endTime);
    this.updateEditor(-1, undefined, undefined, true);
};

TrackEditor.prototype.onTrimAudio = function() {
    var selected = this.getSelectedArea();

    this.trim(selected.start, selected.end);
    this.deactivateAudioSelection();
};

TrackEditor.prototype.onRemoveAudio = function() {
    var selected = this.getSelectedArea();

    this.removeAudio(selected.start, selected.end);
    this.deactivateAudioSelection();
};

TrackEditor.prototype.setState = function(state) {
    var that = this,
        stateEvents = this.states[state].events,
        stateClasses = this.states[state].classes,
        container = this.container,
        prevState = this.currentState,
        prevStateClasses,
        prevStateEvents = this.prevStateEvents,
        func, event, cl,
        i, len;

    if (prevState) {
        prevStateClasses = this.states[prevState].classes;
       
        for (event in prevStateEvents) {
            container.removeEventListener(event, prevStateEvents[event]);
        }
        this.prevStateEvents = {};

        for (i = 0, len = prevStateClasses.length; i < len; i++) {
            container.classList.remove(prevStateClasses[i]);
        }
    }

    for (event in stateEvents) {

        func = that[stateEvents[event]].bind(that);
        //need to keep track of the added events for later removal since a new function is returned after using "bind"
        this.prevStateEvents[event] = func;
        container.addEventListener(event, func);
    }
    for (i = 0, len = stateClasses.length; i < len; i++) {
            container.classList.add(stateClasses[i]);
        }

    this.currentState = state;
};

TrackEditor.prototype.onStateChange = function() {
    var state = this.config.getState();

    this.setState(state);
};

TrackEditor.prototype.onResolutionChange = function(res) {
    var selected = this.selectedArea;

    this.resolution = res;
    this.drawTrack(this.getBuffer());

    if (this.active === true && this.selectedArea !== undefined) {
        
        this.updateEditor(-1, this.samplesToPixels(selected.start), this.samplesToPixels(selected.end), true);
    }
};

TrackEditor.prototype.isPlaying = function() {
    return this.playout.isScheduled() || this.playout.isPlaying();
};

/*
    startTime, endTime in seconds (float).
*/
TrackEditor.prototype.schedulePlay = function(now, delay, startTime, endTime) { 
    var start,
        duration,
        relPos,
        when = now + delay,
        window = (endTime) ? (endTime - startTime) : undefined,
        cueOffset = this.cues.cuein / this.sampleRate;

    //track has no content to play.
    if (this.endTime <= startTime) return;

    //track does not start in this selection.
    if (window && (startTime + window) < this.startTime) return;


    //track should have something to play if it gets here.

    //the track starts in the future of the cursor position
    if (this.startTime >= startTime) {
        start = 0;
        when = when + this.startTime - startTime; //schedule additional delay for this audio node.
        window = window - (this.startTime - startTime);
        duration = (endTime) ? Math.min(window, this.duration) : this.duration;
    }
    else {
        start = startTime - this.startTime;
        duration = (endTime) ? Math.min(window, this.duration - start) : this.duration - start;
    }

    start = start + cueOffset;

    relPos = startTime - this.startTime;
    this.playout.applyFades(this.fades, relPos, now, delay);
    this.playout.play(when, start, duration);
};

TrackEditor.prototype.scheduleStop = function(when) {
   
    this.playout.stop(when); 
};

TrackEditor.prototype.resetCursor = function() {
    this.selectedArea = undefined;
    this.config.setCursorPos(0);
    this.notifySelectUpdate(0, 0);
};

TrackEditor.prototype.updateEditor = function(cursorPos, start, end, highlighted) {
    var pixelOffset = this.getPixelOffset(),
        selected;
 
    if (this.selectedArea) {   
        //must pass selected area in pixels.
        selected = {
            start: this.samplesToPixels(this.selectedArea.start),
            end: this.samplesToPixels(this.selectedArea.end)
        };
    }

    this.drawer.updateEditor(cursorPos, pixelOffset, start, end, highlighted, selected);
};

TrackEditor.prototype.getTrackDetails = function() {
    var d,
        cues = this.cues;

    d = {
        start: this.startTime,
        end: this.endTime,
        fades: this.fades,
        src: this.src,
        cuein: this.samplesToSeconds(cues.cuein),
        cueout: this.samplesToSeconds(cues.cueout)
    };

    return d;
};

