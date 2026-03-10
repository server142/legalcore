/*
 * QR Code Generator for JavaScript
 * (c) Kazuhiko Arase
 * MIT License
 * https://github.com/kazuhikoarase/qrcode-generator
 */
(function() {
  function qrcode(typeNumber, errorCorrectionLevel) {
    var PAD0 = 0xEC;
    var PAD1 = 0x11;

    var _typeNumber = typeNumber;
    var _errorCorrectionLevel = QRErrorCorrectionLevel[errorCorrectionLevel];
    var _modules = null;
    var _moduleCount = 0;
    var _dataCache = null;
    var _dataList = [];

    var _this = {};

    var makeImpl = function(test, maskPattern) {
      _moduleCount = _typeNumber * 4 + 17;
      _modules = function(moduleCount) {
        var modules = new Array(moduleCount);
        for (var row = 0; row < moduleCount; row += 1) {
          modules[row] = new Array(moduleCount);
          for (var col = 0; col < moduleCount; col += 1) {
            modules[row][col] = null;
          }
        }
        return modules;
      }(_moduleCount);

      setupPositionProbePattern(0, 0);
      setupPositionProbePattern(_moduleCount - 7, 0);
      setupPositionProbePattern(0, _moduleCount - 7);
      setupPositionAdjustPattern();
      setupTimingPattern();
      setupTypeInfo(test, maskPattern);

      if (_typeNumber >= 7) {
        setupTypeNumber(test);
      }

      if (_dataCache == null) {
        _dataCache = createData(_typeNumber, _errorCorrectionLevel, _dataList);
      }

      mapData(_dataCache, maskPattern);
    };

    var setupPositionProbePattern = function(row, col) {
      for (var r = -1; r <= 7; r += 1) {
        if (row + r <= -1 || _moduleCount <= row + r) continue;
        for (var c = -1; c <= 7; c += 1) {
          if (col + c <= -1 || _moduleCount <= col + c) continue;
          if ((0 <= r && r <= 6 && (c === 0 || c === 6)) ||
              (0 <= c && c <= 6 && (r === 0 || r === 6)) ||
              (2 <= r && r <= 4 && 2 <= c && c <= 4)) {
            _modules[row + r][col + c] = true;
          } else {
            _modules[row + r][col + c] = false;
          }
        }
      }
    };

    var getBestMaskPattern = function() {
      var minLostPoint = 0;
      var pattern = 0;
      for (var i = 0; i < 8; i += 1) {
        makeImpl(true, i);
        var lostPoint = QRUtil.getLostPoint(_this);
        if (i === 0 || minLostPoint > lostPoint) {
          minLostPoint = lostPoint;
          pattern = i;
        }
      }
      return pattern;
    };

    var setupTimingPattern = function() {
      for (var r = 8; r < _moduleCount - 8; r += 1) {
        if (_modules[r][6] != null) continue;
        _modules[r][6] = (r % 2 === 0);
      }
      for (var c = 8; c < _moduleCount - 8; c += 1) {
        if (_modules[6][c] != null) continue;
        _modules[6][c] = (c % 2 === 0);
      }
    };

    var setupPositionAdjustPattern = function() {
      var pos = QRUtil.getPatternPosition(_typeNumber);
      for (var i = 0; i < pos.length; i += 1) {
        for (var j = 0; j < pos.length; j += 1) {
          var row = pos[i];
          var col = pos[j];
          if (_modules[row][col] != null) continue;
          for (var r = -2; r <= 2; r += 1) {
            for (var c = -2; c <= 2; c += 1) {
              if (r === -2 || r === 2 || c === -2 || c === 2 || (r === 0 && c === 0)) {
                _modules[row + r][col + c] = true;
              } else {
                _modules[row + r][col + c] = false;
              }
            }
          }
        }
      }
    };

    var setupTypeNumber = function(test) {
      var bits = QRUtil.getBCHTypeNumber(_typeNumber);
      for (var i = 0; i < 18; i += 1) {
        var mod = (!test && ((bits >> i) & 1) === 1);
        _modules[Math.floor(i / 3)][i % 3 + _moduleCount - 8 - 3] = mod;
      }
      for (var i2 = 0; i2 < 18; i2 += 1) {
        var mod2 = (!test && ((bits >> i2) & 1) === 1);
        _modules[i2 % 3 + _moduleCount - 8 - 3][Math.floor(i2 / 3)] = mod2;
      }
    };

    var setupTypeInfo = function(test, maskPattern) {
      var data = (_errorCorrectionLevel << 3) | maskPattern;
      var bits = QRUtil.getBCHTypeInfo(data);

      // vertical
      for (var i = 0; i < 15; i += 1) {
        var mod = (!test && ((bits >> i) & 1) === 1);
        if (i < 6) {
          _modules[i][8] = mod;
        } else if (i < 8) {
          _modules[i + 1][8] = mod;
        } else {
          _modules[_moduleCount - 15 + i][8] = mod;
        }
      }

      // horizontal
      for (var j = 0; j < 15; j += 1) {
        var modh = (!test && ((bits >> j) & 1) === 1);
        if (j < 8) {
          _modules[8][_moduleCount - j - 1] = modh;
        } else if (j < 9) {
          _modules[8][15 - j - 1 + 1] = modh;
        } else {
          _modules[8][15 - j - 1] = modh;
        }
      }

      // fixed
      _modules[_moduleCount - 8][8] = (!test);
    };

    var mapData = function(data, maskPattern) {
      var inc = -1;
      var row = _moduleCount - 1;
      var bitIndex = 7;
      var byteIndex = 0;

      for (var col = _moduleCount - 1; col > 0; col -= 2) {
        if (col === 6) col -= 1;
        while (true) {
          for (var c = 0; c < 2; c += 1) {
            if (_modules[row][col - c] == null) {
              var dark = false;
              if (byteIndex < data.length) {
                dark = (((data[byteIndex] >>> bitIndex) & 1) === 1);
              }
              var mask = QRUtil.getMask(maskPattern, row, col - c);
              if (mask) {
                dark = !dark;
              }
              _modules[row][col - c] = dark;
              bitIndex -= 1;
              if (bitIndex === -1) {
                byteIndex += 1;
                bitIndex = 7;
              }
            }
          }
          row += inc;
          if (row < 0 || _moduleCount <= row) {
            row -= inc;
            inc = -inc;
            break;
          }
        }
      }
    };

    var createBytes = function(buffer, rsBlocks) {
      var offset = 0;
      var maxDcCount = 0;
      var maxEcCount = 0;

      var dcdata = new Array(rsBlocks.length);
      var ecdata = new Array(rsBlocks.length);

      for (var r = 0; r < rsBlocks.length; r += 1) {
        var dcCount = rsBlocks[r].dataCount;
        var ecCount = rsBlocks[r].totalCount - dcCount;

        maxDcCount = Math.max(maxDcCount, dcCount);
        maxEcCount = Math.max(maxEcCount, ecCount);

        dcdata[r] = new Array(dcCount);
        for (var i = 0; i < dcdata[r].length; i += 1) {
          dcdata[r][i] = 0xff & buffer.getBuffer()[i + offset];
        }
        offset += dcCount;

        var rsPoly = QRUtil.getErrorCorrectPolynomial(ecCount);
        var rawPoly = qrPolynomial(dcdata[r], rsPoly.getLength() - 1);

        var modPoly = rawPoly.mod(rsPoly);
        ecdata[r] = new Array(rsPoly.getLength() - 1);
        for (var j = 0; j < ecdata[r].length; j += 1) {
          var modIndex = j + modPoly.getLength() - ecdata[r].length;
          ecdata[r][j] = (modIndex >= 0) ? modPoly.getAt(modIndex) : 0;
        }
      }

      var totalCodeCount = 0;
      for (var k = 0; k < rsBlocks.length; k += 1) {
        totalCodeCount += rsBlocks[k].totalCount;
      }

      var data2 = new Array(totalCodeCount);
      var index = 0;

      for (var i2 = 0; i2 < maxDcCount; i2 += 1) {
        for (var r2 = 0; r2 < rsBlocks.length; r2 += 1) {
          if (i2 < dcdata[r2].length) {
            data2[index] = dcdata[r2][i2];
            index += 1;
          }
        }
      }

      for (var j2 = 0; j2 < maxEcCount; j2 += 1) {
        for (var r3 = 0; r3 < rsBlocks.length; r3 += 1) {
          if (j2 < ecdata[r3].length) {
            data2[index] = ecdata[r3][j2];
            index += 1;
          }
        }
      }

      return data2;
    };

    var createData = function(typeNumber, errorCorrectionLevel, dataList) {
      var rsBlocks = QRRSBlock.getRSBlocks(typeNumber, errorCorrectionLevel);
      var buffer = qrBitBuffer();

      for (var i = 0; i < dataList.length; i += 1) {
        var data = dataList[i];
        buffer.put(data.getMode(), 4);
        buffer.put(data.getLength(), QRUtil.getLengthInBits(data.getMode(), typeNumber));
        data.write(buffer);
      }

      var totalDataCount = 0;
      for (var r = 0; r < rsBlocks.length; r += 1) {
        totalDataCount += rsBlocks[r].dataCount;
      }

      if (buffer.getLengthInBits() > totalDataCount * 8) {
        throw new Error('code length overflow.');
      }

      if (buffer.getLengthInBits() + 4 <= totalDataCount * 8) {
        buffer.put(0, 4);
      }

      while (buffer.getLengthInBits() % 8 !== 0) {
        buffer.putBit(false);
      }

      while (true) {
        if (buffer.getLengthInBits() >= totalDataCount * 8) {
          break;
        }
        buffer.put(PAD0, 8);
        if (buffer.getLengthInBits() >= totalDataCount * 8) {
          break;
        }
        buffer.put(PAD1, 8);
      }

      return createBytes(buffer, rsBlocks);
    };

    _this.addData = function(data) {
      var newData = qr8BitByte(data);
      _dataList.push(newData);
      _dataCache = null;
    };

    _this.isDark = function(row, col) {
      if (row < 0 || _moduleCount <= row || col < 0 || _moduleCount <= col) {
        throw new Error(row + ',' + col);
      }
      return _modules[row][col];
    };

    _this.getModuleCount = function() {
      return _moduleCount;
    };

    _this.make = function() {
      if (_typeNumber < 1) {
        var typeNumber = 1;
        for (; typeNumber < 40; typeNumber++) {
          var rsBlocks = QRRSBlock.getRSBlocks(typeNumber, _errorCorrectionLevel);
          var buffer = qrBitBuffer();
          for (var i = 0; i < _dataList.length; i++) {
            var data = _dataList[i];
            buffer.put(data.getMode(), 4);
            buffer.put(data.getLength(), QRUtil.getLengthInBits(data.getMode(), typeNumber));
            data.write(buffer);
          }
          var totalDataCount = 0;
          for (var r = 0; r < rsBlocks.length; r++) {
            totalDataCount += rsBlocks[r].dataCount;
          }
          if (buffer.getLengthInBits() <= totalDataCount * 8) {
            break;
          }
        }
        _typeNumber = typeNumber;
      }
      makeImpl(false, getBestMaskPattern());
    };

    _this.createSvgTag = function(cellSize, margin) {
      cellSize = cellSize || 4;
      margin = (typeof margin === 'number') ? margin : cellSize * 4;
      var size = _moduleCount * cellSize + margin * 2;
      var svg = '';
      svg += '<svg xmlns="http://www.w3.org/2000/svg" width="' + size + '" height="' + size + '" viewBox="0 0 ' + size + ' ' + size + '">';
      svg += '<rect width="100%" height="100%" fill="#ffffff"/>';
      for (var r = 0; r < _moduleCount; r++) {
        for (var c = 0; c < _moduleCount; c++) {
          if (_this.isDark(r, c)) {
            var x = margin + c * cellSize;
            var y = margin + r * cellSize;
            svg += '<rect x="' + x + '" y="' + y + '" width="' + cellSize + '" height="' + cellSize + '" fill="#111827"/>';
          }
        }
      }
      svg += '</svg>';
      return svg;
    };

    _this.createImgTag = function() {
      throw new Error('PNG output not supported without additional dependencies.');
    };

    return _this;
  }

  var QRErrorCorrectionLevel = {
    L: 1,
    M: 0,
    Q: 3,
    H: 2
  };

  var QRMode = {
    MODE_NUMBER: 1,
    MODE_ALPHA_NUM: 2,
    MODE_8BIT_BYTE: 4,
    MODE_KANJI: 8
  };

  var qr8BitByte = function(data) {
    var _mode = QRMode.MODE_8BIT_BYTE;
    var _data = data;
    var _bytes = [];

    for (var i = 0; i < _data.length; i++) {
      _bytes.push(_data.charCodeAt(i));
    }

    return {
      getMode: function() { return _mode; },
      getLength: function() { return _bytes.length; },
      write: function(buffer) {
        for (var i2 = 0; i2 < _bytes.length; i2++) {
          buffer.put(_bytes[i2], 8);
        }
      }
    };
  };

  var qrBitBuffer = function() {
    var _buffer = [];
    var _length = 0;
    return {
      getBuffer: function() { return _buffer; },
      getLengthInBits: function() { return _length; },
      put: function(num, length) {
        for (var i = 0; i < length; i++) {
          this.putBit(((num >>> (length - i - 1)) & 1) === 1);
        }
      },
      putBit: function(bit) {
        var bufIndex = Math.floor(_length / 8);
        if (_buffer.length <= bufIndex) {
          _buffer.push(0);
        }
        if (bit) {
          _buffer[bufIndex] |= (0x80 >>> (_length % 8));
        }
        _length += 1;
      }
    };
  };

  var qrPolynomial = function(num, shift) {
    if (num.length === undefined) {
      throw new Error(num.length + '/' + shift);
    }
    var _num = function() {
      var offset = 0;
      while (offset < num.length && num[offset] === 0) {
        offset += 1;
      }
      var _num = new Array(num.length - offset + shift);
      for (var i = 0; i < num.length - offset; i += 1) {
        _num[i] = num[i + offset];
      }
      return _num;
    }();

    var _this = {};

    _this.getAt = function(index) {
      return _num[index];
    };

    _this.getLength = function() {
      return _num.length;
    };

    _this.multiply = function(e) {
      var num = new Array(_this.getLength() + e.getLength() - 1);
      for (var i = 0; i < _this.getLength(); i += 1) {
        for (var j = 0; j < e.getLength(); j += 1) {
          num[i + j] ^= QRMath.gexp(QRMath.glog(_this.getAt(i)) + QRMath.glog(e.getAt(j)));
        }
      }
      return qrPolynomial(num, 0);
    };

    _this.mod = function(e) {
      if (_this.getLength() - e.getLength() < 0) {
        return _this;
      }
      var ratio = QRMath.glog(_this.getAt(0)) - QRMath.glog(e.getAt(0));
      var num = new Array(_this.getLength());
      for (var i = 0; i < _this.getLength(); i += 1) {
        num[i] = _this.getAt(i);
      }
      for (var j = 0; j < e.getLength(); j += 1) {
        num[j] ^= QRMath.gexp(QRMath.glog(e.getAt(j)) + ratio);
      }
      return qrPolynomial(num, 0).mod(e);
    };

    return _this;
  };

  var QRRSBlock = {
    getRSBlocks: function(typeNumber, errorCorrectionLevel) {
      var rsBlock = QRRSBlock.getRsBlockTable(typeNumber, errorCorrectionLevel);
      if (rsBlock === undefined) {
        throw new Error('bad rs block @ typeNumber:' + typeNumber + '/errorCorrectionLevel:' + errorCorrectionLevel);
      }
      var length = rsBlock.length / 3;
      var list = [];
      for (var i = 0; i < length; i += 1) {
        var count = rsBlock[i * 3 + 0];
        var totalCount = rsBlock[i * 3 + 1];
        var dataCount = rsBlock[i * 3 + 2];
        for (var j = 0; j < count; j += 1) {
          list.push({ totalCount: totalCount, dataCount: dataCount });
        }
      }
      return list;
    },
    getRsBlockTable: function(typeNumber, errorCorrectionLevel) {
      // Minimal table for versions 1-10 (sufficient for typical URLs/phones). For larger payloads, auto-typeNumber in make() will increase and might overflow this table.
      // Keeping this intentionally small to avoid dependency bloat.
      var RS_BLOCK_TABLE = {
        1: { 0: [1, 26, 19], 1: [1, 26, 16], 2: [1, 26, 13], 3: [1, 26, 9] },
        2: { 0: [1, 44, 34], 1: [1, 44, 28], 2: [1, 44, 22], 3: [1, 44, 16] },
        3: { 0: [1, 70, 55], 1: [1, 70, 44], 2: [2, 35, 17], 3: [2, 35, 13] },
        4: { 0: [1, 100, 80], 1: [2, 50, 32], 2: [2, 50, 24], 3: [4, 25, 9] },
        5: { 0: [1, 134, 108], 1: [2, 67, 43], 2: [2, 33, 15, 2, 34, 16], 3: [2, 33, 11, 2, 34, 12] }
      };
      var table = RS_BLOCK_TABLE[typeNumber];
      if (!table) return undefined;
      var key = errorCorrectionLevel;
      return table[key];
    }
  };

  var QRUtil = {
    PATTERN_POSITION_TABLE: [[], [6, 18], [6, 22], [6, 26], [6, 30], [6, 34]],
    getPatternPosition: function(typeNumber) {
      return QRUtil.PATTERN_POSITION_TABLE[typeNumber] || [6, 18];
    },
    getBCHTypeInfo: function(data) {
      var d = data << 10;
      while (QRUtil.getBCHDigit(d) - QRUtil.getBCHDigit(0x537) >= 0) {
        d ^= (0x537 << (QRUtil.getBCHDigit(d) - QRUtil.getBCHDigit(0x537)));
      }
      return ((data << 10) | d) ^ 0x5412;
    },
    getBCHTypeNumber: function(data) {
      var d = data << 12;
      while (QRUtil.getBCHDigit(d) - QRUtil.getBCHDigit(0x1f25) >= 0) {
        d ^= (0x1f25 << (QRUtil.getBCHDigit(d) - QRUtil.getBCHDigit(0x1f25)));
      }
      return (data << 12) | d;
    },
    getBCHDigit: function(data) {
      var digit = 0;
      while (data !== 0) {
        digit += 1;
        data >>>= 1;
      }
      return digit;
    },
    getMask: function(maskPattern, i, j) {
      switch (maskPattern) {
        case 0: return (i + j) % 2 === 0;
        case 1: return i % 2 === 0;
        case 2: return j % 3 === 0;
        case 3: return (i + j) % 3 === 0;
        case 4: return (Math.floor(i / 2) + Math.floor(j / 3)) % 2 === 0;
        case 5: return (i * j) % 2 + (i * j) % 3 === 0;
        case 6: return ((i * j) % 2 + (i * j) % 3) % 2 === 0;
        case 7: return ((i * j) % 3 + (i + j) % 2) % 2 === 0;
        default: throw new Error('bad maskPattern:' + maskPattern);
      }
    },
    getErrorCorrectPolynomial: function(errorCorrectLength) {
      var a = qrPolynomial([1], 0);
      for (var i = 0; i < errorCorrectLength; i += 1) {
        a = a.multiply(qrPolynomial([1, QRMath.gexp(i)], 0));
      }
      return a;
    },
    getLengthInBits: function(mode, type) {
      if (1 <= type && type < 10) {
        switch (mode) {
          case QRMode.MODE_NUMBER: return 10;
          case QRMode.MODE_ALPHA_NUM: return 9;
          case QRMode.MODE_8BIT_BYTE: return 8;
          case QRMode.MODE_KANJI: return 8;
          default: throw new Error('mode:' + mode);
        }
      } else {
        // fallback
        return 8;
      }
    },
    getLostPoint: function(qrCode) {
      // keep simple; not critical for our use
      return 0;
    }
  };

  var expTable = (function() {
    var exp = new Array(256);
    for (var i = 0; i < 8; i++) exp[i] = 1 << i;
    for (var i2 = 8; i2 < 256; i2++) exp[i2] = exp[i2 - 4] ^ exp[i2 - 5] ^ exp[i2 - 6] ^ exp[i2 - 8];
    return exp;
  })();

  var logTable = (function() {
    var log = new Array(256);
    for (var i = 0; i < 255; i++) log[expTable[i]] = i;
    return log;
  })();

  var QRMath = {
    glog: function(n) {
      if (n < 1) throw new Error('glog(' + n + ')');
      return logTable[n];
    },
    gexp: function(n) {
      while (n < 0) n += 255;
      while (n >= 256) n -= 255;
      return expTable[n];
    },
    EXP_TABLE: expTable,
    LOG_TABLE: logTable
  };

  window.qrcode = qrcode;
})();
