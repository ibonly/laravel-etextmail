<?phps

namespace Ibonly\EtextMail;

use Illuminate\Support\ServiceProvider;

class EtextMailServiceProvider extends ServiceProvider
{
	protected $defer = false;

	public function register()
	{
		$this->app->bind('laravel-etextmail', function() {
			return new EtextMail;
		});
	}

	public function provides()
	{
		return ['laravel-etextmail'];
	}
}