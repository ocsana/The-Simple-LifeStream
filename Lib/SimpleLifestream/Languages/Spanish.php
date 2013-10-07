<?php
/**
 * Spanish.php
 *
 * @package Languages
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace SimpleLifestream\Languages;

/**
 * Translation to the spanish language
 */
class Spanish extends Adapter
{
    /** inline {@inheritdoc} */
    protected $translation = array(
        'link'        => '{link}.',
        'posted'      => 'escribió {link}.',
        'starred'     => 'esta observando a {link}.',
        'followed'    => 'se suscribió a {link}.',
        'commented'   => 'comentó en "{link}".',
        'answered'    => 'contestó la pregunta "{link}".',
        'accepted'    => 'aceptó una respuesta para "{link}".',
        'asked'       => 'preguntó "{link}".',
        'badge'       => 'ganó la insignia {link}.',
        'repo-released' => 'liberó la version {link}',
        'repo-create' => 'creó el proyecto {link}.',
        'repo-push'   => 'actualizó el proyecto {link}.',
        'repo-pull-opened'  => 'creó un pull request para {link}',
        'repo-issue-created'  => 'escribió un reporte para {link}',
        'tweeted'     => 'trinó "{link}".',
        'favorited'   => 'agregó {link} a sus favoritos.',
    );
}
?>
