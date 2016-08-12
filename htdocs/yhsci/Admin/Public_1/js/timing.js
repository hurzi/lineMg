//获取当前的年月日时分秒
function Datetime() {
	var dateTime   = new Date();
	this.currYear  = dateTime.getFullYear();
	this.currMonth = dateTime.getMonth() + 1;
	this.currDate  = dateTime.getDate();
	this.currHour  = dateTime.getHours();
	
	if (this.currHour == 23) {
		this.nextHour = 0;
	}else{
		this.nextHour = this.currHour + 1;
	}
	//this.nextHour = this.currHour ;
	this.currMinute = dateTime.getMinutes();
	// alert(this.currDate+'='+this.currHour+'>'+this.currMinute)

	this.getYear = function() {
		var str = '<select id="yearId">';
		for ( var y = this.currYear; y <= this.currYear+1; y++) {
			if (this.currMonth == y) {
				str += '<option value="' + y + '" selected="selected">' + y
						+ '</option>';
			} else {
				str += '<option value="' + y + '">' + y + '</option>';
			}
		}
		str += '</select>';
		return str;
	}, this.getMonth = function() {
		var str = '<select id="monthId">';
		for ( var i = 1; i <= 12; i++) {
			if (this.currMonth == i) {
				str += '<option value="' + i + '" selected="selected">' + i
						+ '</option>';
			} else {
				str += '<option value="' + i + '">' + i + '</option>';
			}
		}
		str += '</select>';
		return str;
	}, this.getDate = function() {
		var str = '<select id="dateId">';
		for ( var i = 1; i <= this.getCurrMonthDateNum(this.currYear,
				this.currMonth); i++) {
			if (this.currDate == i) {
				str += '<option value="' + i + '" selected="selected">' + i
						+ '</option>';
			} else {
				str += '<option value="' + i + '">' + i + '</option>';
			}
		}
		str += '</select>';
		return str;
	}, this.getHour = function() {
		var str = '<select id="hourId">';
		for ( var i = 0; i <= 23; i++) {
			if (this.nextHour == i) {
				str += '<option value="' + i + '" selected="selected">' + i
						+ '</option>';
			} else {
				str += '<option value="' + i + '">' + i + '</option>';
			}
		}
		str += '</select>';
		return str;
	}, this.getMinute = function() {
		var str = '<select id="minuteId">';
		for ( var i = 0; i <= 59; i++) {
			if (this.currMinute == i) {
				str += '<option value="' + i + '" selected="selected">' + i
						+ '</option>';
			} else {
				str += '<option value="' + i + '">' + i + '</option>';
			}
		}
		str += '</select>';
		return str;
	}
}

/**
 * 根据传递入的年份和月份计算出当前月包含的天数
 * @param year  四位数年份
 * @param month 月份
 * @returns
 */
Datetime.prototype.getCurrMonthDateNum = function(year, month) {
	switch (month) {
	case 1:
		month = 'Jan';
		break;
	case 2:
		month = 'Feb';
		break;
	case 3:
		month = 'Mar';
		break;
	case 4:
		month = 'Apr';
		break;
	case 5:
		month = 'May';
		break;
	case 6:
		month = 'Jun';
		break;
	case 7:
		month = 'Jul';
		break;
	case 8:
		month = 'Aug';
		break;
	case 9:
		month = 'Sep';
		break;
	case 10:
		month = 'Oct';
		break;
	case 11:
		month = 'Nov';
		break;
	case 12:
		month = 'Dec';
		break;
	default:
		month = 'Jan';
	}
	/*
	 * January (Jan) Febuary (Feb) March (Mar) April (Apr) May (May) June (Jun)
	 * July (Jul) August (Aug) September (Sep) October (Oct) November (Nov)
	 * December (Dec)
	 */
	var commonMonth = {
		Jan : 31, // 一月
		Mar : 31, // 三月
		Apr : 30, // 四月
		May : 31, // 五月
		Jun : 30, // 六月
		Jul : 31, // 七月
		Aug : 31, // 八月
		Sep : 30, // 九月
		Oct : 31, // 十月
		Nov : 30, // 十一月
		Dec : 31
	// 十二月
	};

	if (month == 2) {// 当为二月份是计算
		var leapYearFlag = false;// 是否闰年
		if (year / 4 == 0) {
			leapYearFlag = true;
		}
		if (leapYearFlag) {
			commonMonth.Feb = 29;
		} else {
			commonMonth.Feb = 28;
		}
	}
	return commonMonth[month];
};
