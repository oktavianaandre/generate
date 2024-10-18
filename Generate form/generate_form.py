import os
import pandas as pd
import warnings
from bs4 import BeautifulSoup
import re
import openpyxl
import pyodbc
import shutil

def copy_file(source, destination):
    try:
        shutil.copy2(source, destination)
        print(f"File berhasil disalin dari {source} ke {destination}")
        return True
    except IOError as e:
        print(f"Tidak dapat menyalin file. Error: {e}")
        return False

# Suppress the specific warning
warnings.filterwarnings('ignore', category=UserWarning,message='Data Validation extension is not supported and will be removed')

current_path = os.getcwd()
print(f"Current working directory: {current_path}")

# Create the 'hasil' folder if it does not exist
hasil_folder = os.path.join(current_path, 'hasil')
if not os.path.exists(hasil_folder):
    os.makedirs(hasil_folder)
    print(f"Folder 'hasil' dibuat di {hasil_folder}")

# Read the Excel file
df = pd.read_excel(current_path+'\Form.xlsx', sheet_name='Table', header=None)

# Get the value from cell B1
table = df.iloc[0,1]
print(f"Value in cell B1: {table}")
# Get the value from cell B1
file = df.iloc[1,1]
print(f"Value in cell B2: {file}")

# Initialize an empty list to store the values
searchs = []

start_row = 3 if df.iloc[0,3] == "Yes" else 4

# Iterate from the third row (index 2) to the end
for search in df.iloc[start_row:, 1]:  # Column B is index 1
    # Check if the value is NaN
    if pd.isna(search):
        break
    searchs.append(str(search))  # Convert to string to ensure all values can be joined

# Check the condition in cell D1
if df.iloc[0, 3] == "Yes":
    # Add 'transaction_date' at the end if the condition is Yes
    # Corrected the append method to use a single append call with a list
    searchs.append('status')  # Add 'status'
    searchs.append('last_update_transaction')  # Add 'last_update_transaction'
    searchs.append('status_transaction')  # Add 'status_transaction'

# Join the values
result = "@petik,@petik".join(searchs)

# Replace @petik with '
search = result.replace("@petik", "'")

# Add quotes at the beginning and end
search = "$search = array('" + search + "');\n"

print(search)  # For demonstration purposes

input_transaction_id=f"""
                                           <div class="form-group">
                                              <div class="row">
                                                 <div class="col-md-4">
                                                    <label for="hdrid">TRANSACTION ID</label>
                                                 </div>
                                                 <div class="col-md-8">
                                                    <input type="text" name="hdrid" class="form-control" id="hdrid" placeholder="auto generate" readonly>
                                                 </div>
                                              </div>
                                           </div>\n
"""

input_text=f"""
                                           <div class="form-group">
                                              <div class="row">
                                                 <div class="col-md-4">
                                                    <label for="@field">@label</label>
                                                 </div>
                                                 <div class="col-md-8">
                                                    <input type="text" name="@field" class="form-control" id="@field" placeholder="Please input @label">
                                                 </div>
                                              </div>
                                           </div>
\n
"""

input_select=f"""
                                           <div class="form-group">
                                             <div class="row">
                                               <div class="col-md-4">
                                                 <label>@label</label>
                                               </div>
                                               <div class="col-md-8">
                                                 <select class="form-control select2" id="@field" name="@field" style="width: 100%;">
                                                   <option value='' selected="selected">-Select-</option>
                                                   <?php
                                                     foreach ($@field as $value) @kurung_buka
                                                     echo "<option value='$value->nik'>$value->nik-$value->name</option>";
                                                     @kurung_tutup
                                                   ?>
                                                 </select>
                                               </div>
                                             </div>
                                           </div>\n
"""

input_date=f"""
                                           <div class="form-group">
                                              <div class="row">
                                                 <div class="col-md-4">
                                                   <label>@label:</label>
                                                 </div>
                                                 <div class="col-md-4">
                                                   <div class="input-group date" data-date-format="YYYY-MM-DD" data-target-input="nearest">
                                                     <input type="date" id="@field" name="@field" class="form-control datetimepicker-input" data-target="#@field"/>
                                                   </div>
                                                 </div>
                                              </div>
                                           </div>\n
"""

input_date_time=f"""
                                           <div class="form-group">
                                              <div class="row">
                                                 <div class="col-md-4">
                                                   <label>@label:</label>
                                                 </div>
                                                 <div class="col-md-8">
                                                   <div class="input-group date" data-date-format="YYYY-MM-DD HH:mm:ss"  id="timepicker_@field" data-target-input="nearest">
                                                     <input type="datetime-local" id="@field" name="@field" class="form-control datetimepicker-input" data-target="#timepicker_@field"/>
                                                       <div class="input-group-append" data-target="#timepicker_@field" data-toggle="datetimepicker">
                                                       </div>
                                                   </div>
                                                 </div>
                                              </div>
                                           </div>
\n
"""

input_attach=f"""
                                           <div class="form-group">
                                             <div class="row">
                                               <div class="col-md-3">
                                                 <label for="@field">@label</label>
                                               </div>
                                                <div class="col-md-1">
                                                 <a class="btn btn-success" id="@field_view" target="_blank"><i class="fas fa-eye text-center"></i></a>
                                               </div>
                                               <div class="col-md-8">
                                                  <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="@field" multiple="" name="@field">
                                                    <label class="custom-file-label" for="@field" id="@field_label">Choose file</label>
                                                  </div>
                                               </div>
                                             </div>
                                           </div>
\n
"""

controller_attach=f"""
        if (!empty($_FILES['file']['name'])) @kurung_buka
            $this->upload_file_attach('@field', 'hdrid3');
        @kurung_tutup
"""

rule=f"""
            @field: @kurung_buka
            required: true,
            @kurung_tutup,
"""

message=f"""
            @field: @kurung_buka
            required: "Please Input @label",
            minlength: 3
            @kurung_tutup,
"""

datetime=f"""
        list($date, $time) = explode('T', $post_data['@field']);
                
        // Format tanpa huruf 'T'
        $post_data['@field'] = $date . ' ' . $time;

"""

data_val_select=f"""               $('#@field').select2().val(data.@field).trigger('change');"""

data_val_attach=f"""
                document.getElementById('@field_label').innerHTML=data.@field;
                 var a = document.getElementById('@field_view');
                 if(!data.@field=='')@kurung_buka
                   a.href = "<?php echo base_url('assets/upload/') ?>" + data.@field;
                 @kurung_tutupelse@kurung_buka
                   a.removeAttribute("href");
                 @kurung_tutup;
 \n
 """
data_val=f"""               $('#@field').val(data.@field);\n """

datatable=f"""@kurung_buka"data":"@field"@kurung_tutup,\n            """

attachv=f"""
        document.getElementById('@field').addEventListener('change', function() @kurung_buka
        var fileName = this.files[0].name; // Mengambil nama file yang dipilih
        var label = document.getElementById('@field_label');
        label.innerHTML = fileName; // Mengganti teks label dengan nama file yang dipilih
    @kurung_tutup);\n
"""



attachc=f"""
        if (!empty($_FILES['@field']['name'])) @kurung_buka
            $this->upload_file_attach('@field', $hdrid, '@tb');
        @kurung_tutup
"""

view_file=f"""
        $('#@field_view').show();\n
"""

hide_file=f"""
        $('#@field_view').hide();\n
"""

add=f"""
        $('#@field').select2().val(null).trigger('change');\n
"""

view=f"""document.getElementById('@field_label').innerHTML = 'Choose File';\n"""

btnsend=f"""<button type="button" onclick="Send_mail()" class="btn btn-success waves-effect waves-light "
                                            id="btnsend">Send
                                        </button>"""

btntabel=f"""mnu += '<a class="btn btn-info btn-icon rounded-pill btn-sm " href="' + 
                    '<?php echo base_url('C_report/doc?var1='); ?>' + 
                    row.hdrid + 
                    '" ><span class="fa fa-book"></span></a>';"""

caprov=f"""$nik = $this->input->post('nik');
        $date_transaction = mdate('%Y-%m-%d %H:%i:%s',time());
        $this->M_@file->add_approval1($date_transaction,$hdrid,$nik,'1','QMS Admin');
        $this->M_@file->add_approval2($date_transaction,$hdrid,$nik,'2','PIC');
        $this->M_@file->add_approval3($date_transaction,$hdrid,$nik,'3','Superior 1');
        $this->M_@file->add_approval4($date_transaction,$hdrid,$nik,'4','Superior 2');
        $this->M_@file->add_approval5($date_transaction,$hdrid,$nik,'5','MR');"""

maprov=f"""function add_approval2($date_transaction,$hdrid,$nik,$sequence,$position)
   @kurung_buka
       $query = $this->db->query("
           INSERT INTO tb_approval
               (hdrid
               ,transaction_date
               ,nik
               ,name
               ,department_code
               ,department_name
               ,office_email
               ,position_code
               ,position_name
           )
               SELECT '$hdrid'
                   ,'$date_transaction'
                   ,nik
                   ,name
                   ,kode_section
                   ,section
                   ,email_pic
                   ,'$sequence'
                   ,'$position'
               FROM tb_superior
               WHERE nik = '$nik'
           ");
   @kurung_tutup
   function add_approval1($date_transaction,$hdrid,$nik,$sequence,$position)
   @kurung_buka
       $query = $this->db->query("
           INSERT INTO tb_approval
               (hdrid
               ,transaction_date
               ,nik
               ,name
               ,department_code
               ,department_name
               ,office_email
               ,position_code
               ,position_name
           )
               SELECT '$hdrid'
                   ,'$date_transaction'
                   ,nik_admin
                   ,name_admin
                   ,kode_section_admin
                   ,section_admin
                   ,email_admin
                   ,'$sequence'
                   ,'$position'
               FROM tb_superior
               WHERE nik = '$nik'
           ");
   @kurung_tutup
   function add_approval3($date_transaction,$hdrid,$nik,$sequence,$position)
   @kurung_buka
       $query = $this->db->query("
           INSERT INTO tb_approval
               (hdrid
               ,transaction_date
               ,nik
               ,name
               ,department_code
               ,department_name
               ,office_email
               ,position_code
               ,position_name
           )
               SELECT '$hdrid'
                   ,'$date_transaction'
                   ,nik_superior
                   ,name_superior
                   ,kode_setion_superior
                   ,name_section_superior
                   ,email_superior
                   ,'$sequence'
                   ,'$position'
               FROM tb_superior
               WHERE nik = '$nik'
           ");
   @kurung_tutup
   function add_approval4($date_transaction,$hdrid,$nik,$sequence,$position)
   @kurung_buka
       $query = $this->db->query("
           INSERT INTO tb_approval
               (hdrid
               ,transaction_date
               ,nik
               ,name
               ,department_code
               ,department_name
               ,office_email
               ,position_code
               ,position_name
           )
               SELECT '$hdrid'
                   ,'$date_transaction'
                   ,nik_superior2
                   ,name_superior2
                   ,kode_setion_superior2
                   ,name_section_superior2
                   ,email_superior2
                   ,'$sequence'
                   ,'$position'
               FROM tb_superior
               WHERE nik = '$nik'
           ");
   @kurung_tutup
   function add_approval5($date_transaction,$hdrid,$nik,$sequence,$position)
   @kurung_buka
       $query = $this->db->query("
           INSERT INTO tb_approval
               (hdrid
               ,transaction_date
               ,nik
               ,name
               ,department_code
               ,department_name
               ,office_email
               ,position_code
               ,position_name
           )
               SELECT '$hdrid'
                   ,'$date_transaction'
                   ,nik_mr
                   ,name_mr
                   ,kode_setion_mr
                   ,name_section_mr
                   ,email_mr
                   ,'$sequence'
                   ,'$position'
               FROM tb_superior
               WHERE nik = '$nik'
           ");
   @kurung_tutup"""

superior=f"""$data['nik'] = $this->db->get_where('tb_superior')->result();"""

delete=f"""$this->M_@file_name->Delete_Data($where,'tb_approval');"""

field_table_null=f"""[@field] @type NULL,\n"""
field_table_notnull=f"""[@field] @type NOT NULL,\n"""

status=f"""<th>STATUS</th>
                                                          <th>LAST UPDATE TRANSACTION</th>
                                                          <th>STATUS TRANSACTION</th>"""

transct=f"""@kurung_buka"data":"status"@kurung_tutup,
            @kurung_buka"data":"last_update_transaction"@kurung_tutup,
            @kurung_buka"data":"status_transaction"@kurung_tutup,"""

sendc=f"""$approver = $this->M_@file->cari_approval($hdrid);
        $superior = $this->M_@file->cari_tb_superior($nik);
        $status_transaction ="Waiting Approve by " .$approver->name ." (" . $approver->position_name .')' ;
        $post_data3=array('transaction_date'=>mdate('%Y-%m-%d',time()),
        'status_transaction' => $status_transaction,
        'last_update_transaction' => $date_transaction,
        'status' => 'Send');"""

sendmail=f"""$email_data = array(
            'hdrid'=>$hdrid,
            'transaction_date'=>mdate('%Y-%m-%d %H:%i:%s',time()),
            'nik'=>$approver->nik,
            'name'=>$approver->name,
            'department_code'=>$approver->department_code,
            'department_name'=>$approver->department_name,
            // 'office_email'=>$approver->office_email,
            'office_email'=>'fadhilahm282@gmail.com',
            'position_code'=>'1',
            'position_name'=>'QMS Admin',
            'subject_email'=>"Add Procedure",
            'body_content'=>'Open',
            'comment'=> '',
            // 'cc_email'=>'');
            'cc_email'=>$superior->email_pic.';'.$superior->email_admin.';'.$superior->email_superior.';'.$superior->email_superior2.';'.$superior->email_mr);

        $this->M_email->send_mail($email_data);"""


#set default kosong
html=""
#set default column Action
th="  <th>ACTION</th>\n"
# tambahkan default transaktion id
form=input_transaction_id
# Set default kosong
rules=""
# Set default kosong
messages=""
# Set default kosong
data_vals=""
# Set default kosong
datatables=""
# Set default kosong
excel_imports=""
# Set default kosong
controller_attachs=""
attachvs=""
views=""
attachcs=""
datetimes=""
export_excel=""
hide_files=""
view_files=""
adds=""
# btnsends=""
btntabels=""
caprovs=""
maprovs=""
superiors=""
deletes=""
statuss=""
transcts=""
sendcs=""
sendmails=""

start_row = 3 if df.iloc[0,3] == "Yes" else 4
create_table=f"""CREATE TABLE [dbo].[{table}](\n[hdrid] [nvarchar](50) NULL,\n[transaction_date] [date] NULL,\n"""

# Mulai iterasi dari baris ke-3 (indeks 2)
for index, row in df.iloc[start_row:].iterrows():
    # Cek jika nilai pada kolom pertama adalah NaN
    if pd.isna(row[0]):
        break
    # TH
    th += f'''                                                          <th>{row[0].upper()}</th>\n'''
    
    if row[2]=="Text Box":
        form += input_text
    elif row[2]=="Select Filter":
        form += input_select
    elif row[2]=="Attach File":
        form += input_attach
    elif row[2]=="Date": 
        form += input_date
    elif row[2]=="Date Time": 
        form += input_date_time
    else:
        form += input_text

    form=form.replace("@field",row[1])
    form=form.replace("@label",row[0].upper())
    form=form.replace("@kurung_buka","{")
    form=form.replace("@kurung_tutup","}")

    # tambahkan rule
    if row [4] == "Yes":
        rules += rule
    else:
        rules += ""
    rules=rules.replace("@field",row[1])
    rules=rules.replace("@kurung_buka","{")
    rules=rules.replace("@kurung_tutup","}")

    #datetime
    if row [2] == "Date Time":
     datetimes += datetime
     datetimes = datetime.replace("@field",row[1])

    # tambahkan if 
    if row [4] == "Yes":
        messages += message
    else:
        messages += ""
    
    messages=messages.replace("@field",row[1])
    messages=messages.replace("@label",row[0].upper())
    messages=messages.replace("@kurung_buka","{")
    messages=messages.replace("@kurung_tutup","}")
    
    if row[2] == "Select Filter":
     adds += add
     adds = adds.replace("@field", row[1])

    # tambahkan if
    if row [2] == "Select Filter":
      data_vals += data_val_select
    elif row [2] == "Attach File":
      data_vals += data_val_attach
    else:
        data_vals += data_val
    data_vals=data_vals.replace("@field",row[1])
    data_vals=data_vals.replace("@kurung_buka","{")
    data_vals=data_vals.replace("@kurung_tutup","}")
    
    
    datatables += datatable
    datatables=datatables.replace("@field",row[1])
    datatables=datatables.replace("@kurung_buka","{")
    datatables=datatables.replace("@kurung_tutup","}")

    # approval
    
    # if df.iloc[0,3] == "Yes" and btnsends == "":
    #     btnsends = btnsend
    
    if df.iloc[0,3] == "Yes" and btntabels == "":
        btntabels = btntabel
    
    if df.iloc[0,3] == "Yes" and caprovs == "":
        caprovs = caprov
        caprovs = caprovs.replace("@file", df.iloc[1,1])
        
    if df.iloc[0,3] == "Yes" and caprovs == "":
        deletes = delete
        deletes = deletes.replace("@file", df.iloc[1,1])

    if df.iloc[0,3] == "Yes" and superiors == "":
        superiors = superior

    if df.iloc[0,3] == "Yes" and statuss == "":
        statuss = status

    if df.iloc[0,3] == "Yes" and sendcs == "":
        sendcs = sendc
        sendcs = sendcs.replace("@file", df.iloc[1,1])

    if df.iloc[0,3] == "Yes" and sendmails == "":
        sendmails = sendmail

    if df.iloc[0,3] == "Yes" and transcts == "":
        transcts = transct
        transcts = transcts.replace("@kurung_buka", "{")
        transcts = transcts.replace("@kurung_tutup", "}")

    if df.iloc[0,3] == "Yes" and maprovs == "":
        maprovs = maprov
        maprovs = maprovs.replace("@kurung_buka", "{")
        maprovs = maprovs.replace("@kurung_tutup", "}")

    # tambahkan datatable
    if row [2] == "Attach File": 
      controller_attachs += controller_attach
      controller_attachs=controller_attachs.replace("@field",row[1])
      controller_attachs=controller_attachs.replace("@kurung_buka","{")
      controller_attachs=controller_attachs.replace("@kurung_tutup","}")

    if row[2] == "Attach File":
     attachcs += attachc
     attachcs = attachcs.replace("@field", row[1])
     attachcs = attachcs.replace("@kurung_buka", "{")
     attachcs = attachcs.replace("@kurung_tutup", "}")
     attachcs = attachcs.replace("@tb", df.iloc[0,1])

    if row[2] == "Attach File":
     views += view
     views = views.replace("@field", row[1])

    if row[2] == "Attach File":
     hide_files += hide_file
     hide_files = hide_files.replace("@field", row[1])

    if row[2] == "Attach File":
     view_files += view_file
     view_files = view_files.replace("@field", row[1])

    if row[2] == "Attach File": 
     attachvs += attachv
     attachvs = attachvs.replace("@field", row[1])
     attachvs = attachvs.replace("@kurung_buka", "{")
     attachvs = attachvs.replace("@kurung_tutup", "}")


    excel_imports +=f"""$resultData[$index]['@field'] = ucwords($value['{chr(index-3 + 65)}']);\n                           """
    excel_imports=excel_imports.replace("@field",row[1])
    excel_imports=excel_imports.replace("@kurung_buka","{")

    export_excel +=f"""'@field' => 'ubah_targetKolom',\n            """
    export_excel=export_excel.replace("@field",row[1])


    # tambahkan if
  
    if row [4] == "Yes":
        create_table += field_table_notnull
    else:     
        create_table += field_table_null
    
    create_table=create_table.replace("@field",row[1])
    create_table=create_table.replace("@type",row[3])
    
    html +=form

#tambahkan kurung tutup   
create_table += ")"

print(th)
print(rules)
print(messages)
print(data_vals)
print(datatables)
print(controller_attachs)
print(search)
print(excel_imports)
print(create_table)
print(attachvs)
print(attachcs)
print(datetimes)
print(export_excel)
print(views)
print(hide_files)
print(view_files)
print(adds)
print(btntabels)
print(caprovs)
print(maprovs)
print(statuss)
print(transcts)
print(sendcs)
print(sendmails)

# Setelah membaca nilai dari Excel
print(f"Value in cell B2: {file}")

if df.iloc[0,3] == "Yes":
# Define paths for templates
  template_path = r'D:\Generate form\Template\V_file_name.php'
  template_path_c = r'D:\Generate form\Template\C_file_name.php'
  template_path_d = r'D:\Generate form\Template\M_file_name.php'
  #tanda
  template_path_e = r'D:\Generate form\Template\M_email.php'
  template_path_f = r'D:\Generate form\Template\C_Email.php'
  template_path_g = r'D:\Generate form\Template\M_report.php'
  template_path_h = r'D:\Generate form\Template\C_report.php'
  template_path_i = r'D:\Generate form\Template\V_report.php'

# Define destination paths within the 'hasil' folder
  destination_path = os.path.join(hasil_folder, f"V_{file}.php")
  destination_path_c = os.path.join(hasil_folder, f"C_{file}.php")
  destination_path_d = os.path.join(hasil_folder, f"M_{file}.php")
  #tanda
  destination_path_e = os.path.join(hasil_folder, f"M_email.php")
  destination_path_f = os.path.join(hasil_folder, f"C_Email.php")
  destination_path_g = os.path.join(hasil_folder, f"M_report.php")
  destination_path_h = os.path.join(hasil_folder, f"C_report.php")
  destination_path_i = os.path.join(hasil_folder, f"V_report.php")
else:
  template_path = r'D:\Generate form\Template\V_file_name.php'
  template_path_c = r'D:\Generate form\Template\C_file_name.php'
  template_path_d = r'D:\Generate form\Template\M_file_name.php'

# Define destination paths within the 'hasil' folder
  destination_path = os.path.join(hasil_folder, f"V_{file}.php")
  destination_path_c = os.path.join(hasil_folder, f"C_{file}.php")
  destination_path_d = os.path.join(hasil_folder, f"M_{file}.php")

def replace_placeholder_in_file(file_path, placeholder, replacement):
    try:
        with open(file_path, 'r') as file:
            content = file.read()
        
        updated_content = content.replace(placeholder, replacement)
        
        with open(file_path, 'w') as file:
            file.write(updated_content)
        
        print(f"Placeholder '{placeholder}' berhasil diganti dengan '{replacement}' dalam file {file_path}")
    
    except FileNotFoundError:
        print(f"Error: File '{file_path}' tidak ditemukan.")
    except IOError:
        print(f"Error: Terjadi kesalahan saat membaca atau menulis file '{file_path}'.")
    except Exception as e:
        print(f"Error: Terjadi kesalahan yang tidak terduga: {str(e)}")

def create_excel_template_from_form(form_file, template_file):
    # Baca data dari form.xlsx
    df = pd.read_excel(form_file, sheet_name='Table', header=None)

    # Buat workbook Excel baru dan pilih worksheet aktif
    wb = openpyxl.Workbook()
    ws = wb.active

    # Tentukan baris awal berdasarkan kondisi
    start_row = 3 if df.iloc[0, 3] == "Yes" else 4

    # Tulis data dari DataFrame ke template Excel
    output_col = 1
    for row in range(start_row, len(df)):
        value = df.iloc[row, 0]  # Selalu ambil dari kolom A
        if pd.isna(value):  # Berhenti jika nilai di kolom A adalah NaN
            break
        if pd.notna(value):  # Hanya tulis jika ada nilai
            ws.cell(row=1, column=output_col, value=str(value).upper())  # Isi dengan nilai kapital
            output_col += 1

    # Simpan template Excel baru
    wb.save(template_file)
    print(f"Template Excel berhasil dibuat di {template_file}")

# Buat template Excel dari form.xlsx
form_file_path = os.path.join(os.getcwd(), 'Form.xlsx')
template_file_path = os.path.join(os.getcwd(), 'hasil', 'Template.xlsx')
create_excel_template_from_form(form_file_path, template_file_path)

def process_files(file_replacements):
    for file_path, placeholder, replacement in file_replacements:
        replace_placeholder_in_file(file_path, placeholder, replacement)

# Copy file and replace placeholders
if copy_file(template_path, destination_path):
    file_replacements = [
        (destination_path, '@th', th),
        (destination_path, '@messages', messages),
        (destination_path, '@rules', rules),
        (destination_path, '@data_vals', data_vals),
        (destination_path, '@datatables', datatables),
        (destination_path, '@form', form),
        (destination_path, '@file_name', file),
        (destination_path, '@field', file),
        (destination_path, '//@modal', views),
        (destination_path, '//@attachv', attachvs),
        (destination_path, '//@hide', hide_files),
        (destination_path, '//@adds', adds),
        (destination_path, '@btntabel', btntabels),
        (destination_path, '<!-- @status -->', statuss),
        (destination_path, '//@transct', transcts),
        (destination_path, '//@view', view_files)
    ]
    
    process_files(file_replacements)
    
    print(f"Penggantian selesai dilakukan pada file {destination_path}")
else:
    print("Gagal menyalin file. Penggantian tidak dilakukan.")

# Process C_template
if copy_file(template_path_c, destination_path_c):
    file_replacements_c = [
        (destination_path_c, '@excel_imports', excel_imports),
        (destination_path_c, '@searchs', search),
        (destination_path_c, '@upload_file_attach', controller_attachs),
        (destination_path_c, '@tables', table),
        (destination_path_c, '@file_name', file),
        (destination_path_c, '//@datetime', datetimes),
        (destination_path_c, '@field', file),
        (destination_path_c, '@export_excel', export_excel),
        (destination_path_c, '//@caprov', caprovs),
        (destination_path_c, '//@superior', superiors),
        (destination_path_c, '//@delete', deletes),
        (destination_path_c, '//@sendc', sendcs),
        (destination_path_c, '//@sendmail', sendmails),
        (destination_path_c, '//@attachc', attachcs)
    ]
    
    process_files(file_replacements_c)
    
    print(f"Penggantian selesai dilakukan pada file {destination_path_c}")
else:
    print("Gagal menyalin file C_template.php. Penggantian tidak dilakukan.")
    
# Process M_template
if copy_file(template_path_d, destination_path_d):
    file_replacements_d = [
        (destination_path_d, '@file_name', file),
        (destination_path_d, '//@maprov', maprovs),
        (destination_path_d, '@tables', table)
    ]
    
    process_files(file_replacements_d)
    
    print(f"Penggantian selesai dilakukan pada file {destination_path_d}")
else:
    print(f"Gagal menyalin file M_template.php. Penggantian tidak dilakukan.")
#batas d

if copy_file(template_path_e, destination_path_e):
    
    
    print(f"Penggantian selesai dilakukan pada file {destination_path_e}")
else:
    print(f"Gagal menyalin file M_template.php. Penggantian tidak dilakukan.")
#batas e

if copy_file(template_path_f, destination_path_f):
    file_replacements_f = [
        (destination_path_f, '@file_name', file),
    ]

    process_files(file_replacements_f)
    
    print(f"Penggantian selesai dilakukan pada file {destination_path_f}")
else:
    print(f"Gagal menyalin file M_template.php. Penggantian tidak dilakukan.")
#batas f

if copy_file(template_path_g, destination_path_g):
    
    print(f"Penggantian selesai dilakukan pada file {destination_path_g}")
else:
    print(f"Gagal menyalin file M_template.php. Penggantian tidak dilakukan.")
#batas g

if copy_file(template_path_h, destination_path_h):
    file_replacements_h = [
    (destination_path_h, '@file_name', file),
    ]

    process_files(file_replacements_h)
    
    
    print(f"Penggantian selesai dilakukan pada file {destination_path_h}")
else:
    print(f"Gagal menyalin file M_template.php. Penggantian tidak dilakukan.")
#batas h

if copy_file(template_path_i, destination_path_i):
    file_replacements_i = [
    (destination_path_i, '@file_name', file),
    ]

    process_files(file_replacements_i)

    print(f"Penggantian selesai dilakukan pada file {destination_path_i}")
else:
    print(f"Gagal menyalin file M_template.php. Penggantian tidak dilakukan.")

def buat_koneksi_string(server, database, username, password):
    return (
        f"DRIVER={{SQL Server}};"
        f"SERVER={server};"
        f"DATABASE={database};"
        f"UID={username};"
        f"PWD={password};"
    )

def jalankan_perintah_sql(connection_string, perintah_sql):
    try:
        with pyodbc.connect(connection_string) as conn:
            cursor = conn.cursor()
            cursor.execute(perintah_sql)
            conn.commit()
        print("Perintah SQL berhasil dijalankan.")
    except pyodbc.Error as e:
        print(f"Error saat menjalankan perintah SQL: {e}")

def tambah_kolom_reason_status(connection_string, table):
    sql_check_columns = f"""
    SELECT COUNT(*) AS column_count
    FROM sys.columns 
    WHERE object_id = OBJECT_ID(N'[dbo].[{table}]') 
    AND name IN ('reason', 'status_transaction')
    """
    
    try:
        with pyodbc.connect(connection_string) as conn:
            cursor = conn.cursor()
            cursor.execute(sql_check_columns)
            column_count = cursor.fetchone()[0]
            
            if column_count < 2:
                sql_tambah_kolom = f"""
                ALTER TABLE [dbo].[{table}]
                ADD 
                    [reason] [nvarchar](max) NULL,
                    [status] [nvarchar](100) NULL,
                    [last_update_transaction] [datetime] NULL,
                    [status_transaction] [nvarchar](200) NULL
                """
                cursor.execute(sql_tambah_kolom)
                conn.commit()
                print(f"Kolom 'reason' dan 'status_transaction' berhasil ditambahkan ke tabel '{table}'.")
            else:
                print(f"Kolom 'reason' dan 'status_transaction' sudah ada di tabel '{table}'.")
    except pyodbc.Error as e:
        print(f"Error saat menambahkan kolom: {e}")

def buat_tabel_persetujuan(connection_string, table):
    if df.iloc[0, 3] == "Yes":
        tb_coba_aproval = "tb_approval"
        sql_buat_tabel_persetujuan = f"""
        IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[{tb_coba_aproval}]') AND type in (N'U'))
        BEGIN
            CREATE TABLE [dbo].[{tb_coba_aproval}](
                [hdrid] [nvarchar](50) NULL,
                [transaction_date] [date] NULL,
                [nik] [nvarchar](50) NULL,
                [name] [nvarchar](100) NULL,
                [department_code] [nvarchar](50) NULL,
                [department_name] [nvarchar](100) NULL,
                [office_email] [nvarchar](100) NULL,
                [position_code] [nvarchar](50) NULL,
                [position_name] [nvarchar](100) NULL,
                [date_approve] [datetime] NULL
            )
        END
        """
        jalankan_perintah_sql(connection_string, sql_buat_tabel_persetujuan)
        print(f"Tabel persetujuan '{tb_coba_aproval}' berhasil dibuat.")
        
        # Call the new function to add columns
        tambah_kolom_reason_status(connection_string, table)
    else:
        print("Pembuatan tabel persetujuan dilewati. Kondisi tidak terpenuhi.")

def create_send_mail_procedure(connection_string):
    try:
        # SQL command to create the stored procedure
        create_procedure_sql = """
        CREATE PROCEDURE [dbo].[sp_send_mail]
            @hdrid nvarchar(max) = NULL,
            @nik nvarchar(max) = NULL,
            @name nvarchar(max) = NULL, 
            @department_code nvarchar(max) = NULL, 
            @department_name nvarchar(max) = NULL, 
            @email nvarchar(max) = NULL,
            @position_code nvarchar(max) = NULL,
            @position_name nvarchar(max) = NULL,
            @EmailSubject nvarchar(max) = NULL,
            @Email_body_content nvarchar(max) = NULL,
            @comment nvarchar(max) = NULL, 
            @copy_to varchar(max) = ''
        AS
        BEGIN
            declare @EmailBody nvarchar(max), @EmailData nvarchar(max)

            set @EmailBody = '<html><head></head><body>'
            set @EmailSubject = @EmailSubject + ':' + @hdrid        
            set @EmailBody = @EmailBody + 'Dear Mr./Mrs. varname, <br /><br />'

            if(@EmailSubject like 'Reject%')
            BEGIN
                set @EmailData = '<table cellspacing="0" width="100%">'            
                set @EmailData = @EmailData + '
                    <tr><td width="150">Document No</td><td width="10">:</td><td width="150">'+@hdrid+'</td></tr>
                    <tr><td width="150">Reason</td><td width="10">:</td><td width="150">'+@comment+'</td></tr>
                '
                set @EmailData = @EmailData + '</table><br>' 
                set @EmailBody = @EmailBody + @EmailData 
                set @EmailBody = @EmailBody +'<br><br><br><a href="http://10.73.142.71/eline_process/C_report/doc?var1='+ @hdrid+ '"  > <b> Click to open the document</b> </a>'
                set @EmailBody = @EmailBody + '<br><br><br>The email is automatically sent by the system.<br><br>Kind Regards,<br><br><br>DMIA System<hr>'
            END

            if(@EmailSubject like 'Need%')
            BEGIN
                set @EmailData = '<table cellspacing="0" width="100%">'            
                set @EmailData = @EmailData + '
                    <tr><td width="150">Document No</td><td width="10">:</td><td width="150">'+@hdrid+'</td></tr>
                '
                set @EmailData = @EmailData + '</table><br>' 
                set @EmailBody = @EmailBody + @EmailData 
                set @EmailBody = @EmailBody +'<br><br><br><a href="http://10.73.142.71/eline_process/C_report/doc?var1='+ @hdrid+ '"  > <b> Click to open the document</b> </a>'
                set @EmailBody = @EmailBody + '<br><br><br>The email is automatically sent by the system.<br><br>Kind Regards,<br><br><br>DMIA System<hr>'
            END

            if(@EmailSubject like 'Finish%')
            BEGIN
                set @EmailData = '<table cellspacing="0" width="100%">'            
                set @EmailData = @EmailData + '
                    <tr><td width="150">Document No</td><td width="10">:</td><td width="150">'+@hdrid+'</td></tr>
                '
                set @EmailData = @EmailData + '</table><br>' 
                set @EmailBody = @EmailBody + @EmailData 
                set @EmailBody = @EmailBody +'<br><br><br><a href="http://10.73.142.71/eline_process/C_report/doc?var1='+ @hdrid+ '"  > <b> Click to open the document</b> </a>'
                set @EmailBody = @EmailBody + '<br><br><br>The email is automatically sent by the system.<br><br>Kind Regards,<br><br><br>DMIA System<hr>'
            END

            set @EmailBody = @EmailBody + '</body></html>'
            set @EmailBody = replace(@EmailBody,'varname',@name)

            DECLARE @return_value int

            EXEC @return_value = msdb.dbo.sp_send_dbmail  
                @profile_name = 'Generate Form',  
                @recipients = @email,    
                @copy_recipients = @copy_to,
                @subject = @EmailSubject,
                @body_format = 'HTML',
                @body = @EmailBody;
                
            SELECT 'Return Value' = @return_value
        END
        """
        
        # Execute the create procedure command
        with pyodbc.connect(connection_string) as conn:
            cursor = conn.cursor()
            cursor.execute(create_procedure_sql)
            print("Stored procedure 'sp_send_mail' berhasil dibuat.")
    
    except pyodbc.Error as e:
        print(f"Error saat menjalankan perintah SQL: {e}")

# Example usage
# Variabel untuk server, database, nama pengguna, dan kata sandi
server = '10.73.142.71'
database = 'db_line_process'
username = 'sa'
password = 'Asmo1234*'

# Membuat string koneksi
connection_string = buat_koneksi_string(server, database, username, password)

# Check the condition before creating the procedure
if df.iloc[0, 3] == "Yes":
    # Call the function to create the stored procedure
    create_send_mail_procedure(connection_string)

    # Menjalankan perintah SQL untuk membuat tabel utama
    jalankan_perintah_sql(connection_string, create_table)

    # Membuat tabel persetujuan jika diperlukan
    buat_tabel_persetujuan(connection_string, table)  # Use the variable 'table' for the table name

    # Call the function to add columns after all functions have been executed
    tambah_kolom_reason_status(connection_string, table)  # Use the variable 'table' for the table name