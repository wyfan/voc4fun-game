/** @global XLSX fase */
var controller_flashcard = function ($scope) {

    var _source_file_name = $scope.CONFIG.flashcard_path;

    var _db_name = "flashcard";
    var _db_fields = ["q", "a", "note"];
    
    // -----------------------
    
    var _ctl = {};
    
    // -----------------------
    
    var _status = {};
    
    _status.flashcard_count = 0;
    
    // 註冊
    var _status_key = "flashcard";
    var _status_init = function () {
        return $scope.db_status.add_listener(
                _status_key,
                function (_s) {
                    _ctl.status = _s;
                    _status = _s;
                },
                function () {
                    //return _ctl.status;
                    return _status;
                });
    };
    _status_init();
    
    _ctl.status = _status;
    
    // -----------------------
    
    //$scope.DB.register_table(_db_name, _db_fields);
    
    _ctl.setup = function (_callback) {
        // 從xlsx讀取資料

        //$.console_log(typeof(XLSXReader));
        
            
//        $scope.DB.row_exists(_db_name, function (_result) {
//            if (_result === true) {
//                //$.console_trace("已經建立了");
//                $.trigger_callback(_callback);
//            }
//            else {
//                _create_table();
//            }
//        });
        if ($scope.ls.isTableExists(_db_name) === false) {
            _ctl._create_table(_callback);
        }
        else {
            // 檢查數量
            
            
            //$.trigger_callback(_callback);
            
            _ctl._check_table(_callback);
        }
    };
    
    _ctl._create_table = function (_callback) {
        //$scope.DB.create_table(_db_name, _db_fields, function () {
        //$scope.DB.register_table(_db_name, _db_fields, function () {
        XLSX.ajax_loader(_source_file_name, function (_data) {
            //$.console_log(_data);
            _status.flashcard_count = _data.length;
            _status.flashcard_file_name = _source_file_name;
            $scope.db_status.save_status(_status_key);
            _data = $.array_shuffle(_data);

            //$scope.DB.insert(_db_name, _data, _callback);

            $scope.ls.set(_db_name, _data);
            //_data = $scope.ls.get(_db_name, 0);
            //$.console_trace(_data);

            $.trigger_callback(_callback);
        });
        //});
    };
    
    _ctl._check_table = function (_callback) {
        
        var _flashcard_array_to_json = function (_ary) {
            var _json = {};
            for (var _i = 0; _i < _ary.length; _i++) {
                var _q = _ary[_i].q;
                var _a = _ary[_i].a;
                _json[_q] = _a;
            }
            return _json;
        };
        
        var _flashcard_json_diff = function (_json_main, _json_add) {
            var _json_diff = {};
            
            for (var _q in _json_add) {
                if (typeof(_json_main[_q]) === "undefined") {
                    _json_diff[_q] = _json_add[_q];
                }
            }
            
            return _json_diff;
        };
        
        
        
        XLSX.ajax_loader(_source_file_name, function (_data) {
            //$.console_log(_data);
            //_status.flashcard_count = _data.length;
            //console.log([_status.flashcard_count, _data.length]);
            if (_status.flashcard_count !== _data.length) {
                var _new_data = _data;
                _new_data = $.array_shuffle(_new_data);
                _status.flashcard_count = _data.length;
                
                // 取得原來的資料
                var _data_array = $scope.ls.get(_db_name);
                var _new_data_array = _new_data;
                
                var _data_json = _flashcard_array_to_json(_data_array);
                var _new_data_json = _flashcard_array_to_json(_new_data_array);
                
                var _diff_json = _flashcard_json_diff(_data_json, _new_data_json);
                
                
                for (var _q in _diff_json) {
                    _data.push({
                        q: _q,
                        a: _diff_json[_q]
                    });
                }
                
                $scope.db_status.save_status(_status_key);
                //alert([_status_key, _status.flashcard_count, $scope.ctl_flashcard.status.flashcard_count]);
                $scope.ls.set(_db_name, _data);
                
                //console.log("已經重置");
            }
            else {
                //console.log("沒有重置");
            }
            
            $.trigger_callback(_callback);
        });
        //});
    };
    
    _ctl.get_flashcard = function(_id, _callback) {
//        var _sql = "SELECT * FROM flashcard WHERE id = " + _id;
//        $scope.DB.exec(_sql, function (_data) {
//            var _flashcard;
//            if (_data.length > 0) {
//                _flashcard = _data[0];
//            }
//            $.trigger_callback(_callback, _flashcard);
//        });
        //_id--;
        var _flashcard = $scope.ls.get(_db_name, _id);
        if (typeof(_flashcard) === "object") {
            _flashcard.id = _id;
        }
        //$.console_trace(_flashcard);
        $.trigger_callback(_callback, _flashcard);
        return _flashcard;
    };
    
    _ctl.find_flashcard = function (_q) {
        var _flashcards = $scope.ls.get(_db_name);
        //$.console_trace("find_flashcard", _q);
        for (var _i = 0; _i < _flashcards.length; _i++) {
            if (_flashcards[_i].q === _q) {
                var _flashcard = _flashcards[_i];
                _flashcard.id = _i;
                return _flashcard;
            }
        }
    };
    
    _ctl.get_other_flashcards = function (_exclude_id, _length, _callback) {
        
        var _other_cards = [];
        
        var _random_index = [];
        var _max_index = _status.flashcard_count - 1;
        
        if (_max_index > _length) {
            while (_random_index.length < _length) {
                var _i = Math.floor(Math.random() * _max_index);
                
                while ($.inArray(_i, _random_index) !== -1) {
                    _i++;
                    if (_i === _max_index) {
                        _i = 0;
                    }
                }
                
                if (_exclude_id !== _i) {
                    _random_index.push(_i);
                }
            }
        }
        else {
            for (var _i = 0; _i < _max_index; _i++) {
                _random_index.push(_i + 1);
            }
        }
        
        //_random_index = [0, 5];
        //$.console_trace("random_index", _random_index);
        
        var _loop = function (_i) {
            if (_i < _random_index.length) {
//                var _sql = "SELECT * FROM flashcard WHERE id != " + _exclude_id + " LIMIT " + _random_index[_i] + ", 1";
//                $scope.DB.exec(_sql, function (_row) {
//                    _other_cards.push(_row[0]);
//                    _i++;
//                    _loop(_i);
//                });
                var _flashcard = _ctl.get_flashcard(_random_index[_i]);
                _other_cards.push(_flashcard);
                _i++;
                _loop(_i);
            }
            else {
                $.trigger_callback(_callback, _other_cards);
            }
        };
        
        _loop(0);
    };
    
    // -----------------------
    
    _ctl.set_note = function (_id, _note, _callback) {
//        var _data = {
//            note: _note
//        };
        //var _where_sql = 'id = ' + _id;
        
        //$scope.DB.update(_db_name, _data, _where_sql, _callback);
        var _flashcard = _ctl.get_flashcard(_id);
        _flashcard.note = _note;
        delete _flashcard.id;
        //$.console_trace("輸入筆記 " + _id, _flashcard);
        $scope.ls.set(_db_name, _id, _flashcard);
        $.trigger_callback(_callback);
    };
    
    // -----------------------
    
    $scope.ctl_flashcard = _ctl;
        
};