$(function () {
            /*
             * 生日选择及内部操作方法
             *@param 'dataTime':已选择的时间（格式为" 2014-2-6"）;
             * */
            createDataSelect = function (dataTime) {
                var nowDate = new Date();
                //定义并获取当前年份
                var nowYear = nowDate.getFullYear();
                var showData = "0-0-0";
                //定义选中的时间(已定义则取自定义值，未定义取默认值)
                var selectData = dataTime || showData;
                //分割年月日
                var selectDataSplit = selectData.split("-");
                //取选中的年份并转换为整数
                var checkYear = parseInt(selectDataSplit[0]);
                //取选中的月并转换为整数
                var checkMonth = parseInt(selectDataSplit[1]);
                //取选中的日并转换为整数
                var checkDay = parseInt(selectDataSplit[2]);
                var dateHtml = "";

                //生成年份HTML
                dateHtml += '<select name="year" id="birthday_year" class="select_year select_width">';
                //定义最小年份
                var minYear = nowYear - 80;
                dateHtml += '<option disabled="" value="0">请选择：</option>';
                for (i = nowYear; i > minYear; i--) {
                    if (i == checkYear) {
                        dateHtml += '<option value="' + i + '" selected="selected">' + i + '</option>';
                    } else {
                        dateHtml += '<option value="' + i + '">' + i + '</option>';
                    }
                }
                dateHtml += '</select>';
                dateHtml += '<label class="ml5 mr5">年</label>';

                //生成月份HTML
                dateHtml += '<select name="month" id="birthday_month" class="select_month select_width">';
                dateHtml += '<option disabled="" value="0">请选择：</option>';
                for (i = 1; i < 13; i++) {
                    if (i == checkMonth) {
                        dateHtml += '<option value="' + i + '" selected="selected">' + i + '</option>';
                    } else {
                        dateHtml += '<option value="' + i + '">' + i + '</option>';
                    }
                }
                dateHtml += '</select>';
                dateHtml += '<label class="ml5 mr5">月</label>';

                //生成日的选项
                function createDayOption(year, month, day) {
                    var dayOptionHtml = '';
                    var maxDay = "";
                    if (year != 0 && month != 0) {
                        //很据年月获取相应月份的天数
                        if ($.inArray(month, [1, 3, 5, 7, 8, 10, 12]) != -1) {
                            maxDay = 31;
                        } else if ((year % 4 == 0) && month == 2) {
                            maxDay = 29;
                        } else if (month == 2) {
                            maxDay = 28;
                        } else {
                            maxDay = 30;
                        }
                        for (i = 1; i < maxDay + 1; i++) {
                            if (i == day) {
                                dayOptionHtml += '<option value="' + i + '" selected="selected">' + i + '</option>';
                            } else {
                                dayOptionHtml += '<option value="' + i + '">' + i + '</option>';
                            }
                        }
                    }
                    return dayOptionHtml;
                }

                //生成日HTML
                dateHtml += '<select name="day" id="birthday_day" class="select_day select_width">';
                dateHtml += createDayOption(checkYear, checkMonth, checkDay);
                dateHtml += '</select>';
                dateHtml += '<label class="ml5 mr5">日</label>';
                //HTML转换为jQuery对象
                var dateHtmlObject = $(dateHtml);
                //把jQuery对象放入页面的相应容器
                $('.data_select').append(dateHtmlObject);
                //定义年份和月份的change事件
                dateHtmlObject.filter("#birthday_year,#birthday_month").change(function () {
                    //获取当前选中的年份
                    var selectYear = parseInt($("#birthday_year").val());
                    //获取当前选中的月份
                    var selectMonth = parseInt($("#birthday_month").val());
                    //获取当前选中的日
                    var selectDay = parseInt($("#birthday_day").val());
                    var newOptionHtml = createDayOption(selectYear, selectMonth, selectDay);
                    //更新日的选择项
                    $("#birthday_day").empty().append(newOptionHtml);
                });
            }

            //方法调用
            //createDataSelect("2014-02-10");

        })