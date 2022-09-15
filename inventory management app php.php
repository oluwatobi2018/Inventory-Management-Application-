<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Models\BarangMasuk;

class UpdateTelegram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:telegram';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Running Update Notice on Telegram...';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $barangmasuk = BarangMasuk::with('daftaritem')->get();
        foreach ($barangmasuk as $key => $values) {
            $tanggalmasuk = strtotime(date('d-m-Y'));
            $tanggalexp = strtotime($values->tanggal_exp);
            $hasil = $tanggalexp - $tanggalmasuk;
            $days = $hasil / 60 / 60 / 24;
            $namas = $values->daftaritem->nama_barang;
            if($days > 0){
                if($days > 90){
        
                }else{
                    $text = "== Alimart | Barang Expired ==\n"
                    . "<b>Nama Barang</b>\n"
                    . "$namas\n"
                    . "----------------------------\n"
                    . "<b>Selisih: </b>\n"
                    . "$days - Hari";

                    $update = Telegram::sendMessage([
                        'chat_id' => env('TELEGRAM_CHANNEL_ID', ''),
                        'parse_mode' => 'HTML',
                        'text' => $text
                    ]);

                    $returnVar = NULL;
                    $output = NULL;

                    exec($update,$returnVar,$output);
                }
                
            }else{
                $text = "== Alimart | Barang Expired ==\n"
                . "<b>Nama Barang</b>\n"
                . "$namas\n"
                . "----------------------------\n"
                . "<b>Selisih: </b>\n"
                . "Sudah Expried";

                $update = Telegram::sendMessage([
                    'chat_id' => env('TELEGRAM_CHANNEL_ID', ''),
                    'parse_mode' => 'HTML',
                    'text' => $text
                ]);

                $returnVar = NULL;
                $output = NULL;

                exec($update,$returnVar,$output);
            }
                
        }
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','daftar_id','transaksi_id','qty','satuan','tanggal_keluar','crew_store','approve'];
    protected $table = 'barang_keluars';
    public $incrementing = false;

    public function daftaritem()
    {
        return $this->belongsTo(DaftarItem::class, 'daftar_id');
    }
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarItem extends Model
{
    use HasFactory;
    protected $fillable = ['kode_barang','barcode','nama_barang','qty'];
    protected $table = 'daftar_items';

    public function barangmasuk()
    {
        return $this->hasMany(BarangMasuk::class, 'daftar_id');
    }

    public function barangkeluar()
    {
        return $this->hasMany(BarangKeluar::class, 'daftar_id');
    }
}
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksis';
    protected $fillable = ['transaksi','tanggal_keluar','crew_store','approve','user_approve'];
    
    public function barangkeluar()
    {
        return $this->hasMany(BarangKeluar::class);
    }

}
/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;

