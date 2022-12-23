import { intlFormat } from 'date-fns'

class DateFormatter {

    formatDate(date_str) {
        return this.formatDateFromDate(new Date(date_str))
    }

    formatZeit(time_str) {
        return intlFormat(new Date(time_str), { hour: '2-digit', minute: '2-digit', }, {locale: 'de-DE', })
    }

    formatDateFromDate(date) {
        return intlFormat(date, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', }, {locale: 'de-DE', })
    }    
}

export default DateFormatter