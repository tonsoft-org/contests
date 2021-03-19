import * as FileSaver from "file-saver";
import * as XLSX from "xlsx";

export default class Saver {
    static exportToExcel (name, result, header = []) {
        const file_type = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8";
        const file_extension = ".xlsx";
        const data = result.map((item) => { return Object.values(item); });

        const worksheet = XLSX.utils.aoa_to_sheet([header].concat(data));
        const workbook = {
            Sheets: {data: worksheet, cols: []},
            SheetNames: ["data"],
        };
        const excel_buffer = XLSX.write(workbook, {bookType: "xlsx", type: "array"});
        const file_data = new Blob([excel_buffer], {type: file_type});
        FileSaver.saveAs(file_data, name + file_extension);
    }
}
